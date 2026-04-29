<#
Automates Docker Compose build/up and initial app setup.

Usage (PowerShell):
  .\scripts\docker-setup.ps1

This script waits for `docker` to appear in PATH, runs `docker compose build` and
`docker compose up -d`, then executes composer, artisan and npm tasks inside the
containers. Adjust service names if your `docker-compose.yml` uses different names.
#>

function Wait-ForDocker {
    Write-Host "Waiting for Docker to be available..."
    while (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        Write-Host "docker not found in PATH. Sleeping 5s..."
        Start-Sleep -Seconds 5
    }
    Write-Host "Docker available."
}

function Run-Command {
    param(
        [string]$exe,
        [Parameter(ValueFromRemainingArguments=$true)]
        [string[]]$rest
    )
    $args = $rest
    $display = "$exe $($args -join ' ')"
    Write-Host "==> $display"
    $proc = Start-Process -FilePath $exe -ArgumentList $args -NoNewWindow -Wait -PassThru -ErrorAction SilentlyContinue
    if ($proc.ExitCode -ne 0) {
        Write-Host "Command failed with exit code $($proc.ExitCode): $display"
        throw "Command failed"
    }
}

try {
    Wait-ForDocker

    Write-Host "Building images..."
    Run-Command "docker" "compose" "build" "--no-cache"

    Write-Host "Starting containers..."
    Run-Command "docker" "compose" "up" "-d"

    Write-Host "Marking workspace as safe for git and running composer install inside app container..."
    # mark project dir as safe to avoid 'detected dubious ownership' errors
    Run-Command "docker" "compose" "exec" "-T" "app" "git" "config" "--global" "--add" "safe.directory" "/var/www/html"
    Run-Command "docker" "compose" "exec" "-T" "app" "composer" "install" "--no-interaction" "--prefer-dist" "--optimize-autoloader"

    Write-Host "Preparing .env and generating APP_KEY..."
    # Copy .env if missing, then generate APP_KEY
    Run-Command "docker" "compose" "exec" "-T" "app" "sh" "-c" "if [ ! -f .env ]; then cp .env.example .env; fi"
    Run-Command "docker" "compose" "exec" "-T" "app" "php" "artisan" "key:generate" "--force"

    Write-Host "Running migrations and seeders..."
    Run-Command "docker" "compose" "exec" "-T" "app" "php" "artisan" "migrate" "--force" "--seed"

    Write-Host "Creating storage symlink..."
    try {
        Run-Command "docker" "compose" "exec" "-T" "app" "php" "artisan" "storage:link"
    } catch {
        Write-Host "storage:link failed (may already exist). Continuing..."
    }

    Write-Host "Building frontend assets inside node container..."
    try {
        Run-Command "docker" "compose" "exec" "-T" "node" "npm" "ci"
    } catch {
        Write-Host "npm ci failed (continuing)..."
    }
    try {
        Run-Command "docker" "compose" "exec" "-T" "node" "npm" "run" "build"
    } catch {
        Write-Host "npm run build failed (continuing)..."
    }

    Write-Host "Setup completed. Access the app via the webserver port defined in docker-compose (e.g. http://localhost:8000)"
}
catch {
    Write-Host "Error during setup: $_" -ForegroundColor Red
    exit 1
}
