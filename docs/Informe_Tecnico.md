# Informe Técnico — Biblioteca para simples

Fecha: 2026-04-28

## Resumen

Este documento resume la arquitectura, configuración y los pasos técnicos necesarios
para ejecutar, desarrollar y mantener la aplicación "Biblioteca para simples" (Laravel 8).

## Tecnologías y stack

- Aplicación: Laravel 8 (PHP)
- PHP: 8.4 (imagen Docker `php:8.4-fpm`)
- Frontend: Laravel Mix v6, SASS, Vue 2
- Base de datos: MySQL 8.0 (contenedor)
- Contenedores: Docker Compose (servicios: `app`, `webserver`, `db`, `node`, `mailhog`)
- Testing: PHPUnit 9.6

## Estructura principal del repositorio

- `app/` — controladores, modelos y providers.
- `database/migrations/` — migraciones (tablas: `rol`, `usuario`, `ejemplar`, `autor`, `editorial`, `coleccion`, `wishlist`, `detalle_alquiler`, ...).
- `database/seeders/` — seeders: `RolSeeder`, `AdminAndEjemplarSeeder`, `PopulateAllSeeder`, `UpdateEjemplarImagesSeeder`, `DatabaseSeeder`.
- `resources/views/` — vistas Blade, con carpetas `ejemplares`, `admin`, `auth`, `layouts`, etc.
- `public/book/` — imágenes de portada de libros.
- `docker/` — Dockerfile y configuración (nginx, php, entrypoint).

## Docker / Contenedores

- `docker-compose.yml` define servicios:
  - `app`: PHP-FPM, volumen con el código fuente, variable `DB_HOST=db`.
  - `webserver`: Nginx (puerto 8000 en host -> 80 en contenedor).
  - `db`: MySQL 8.0 (puerto 3306); volumen `dbdata`.
  - `node`: para `npm ci` y compilación de assets.
  - `mailhog`: interfaz web en el host `http://localhost:8025`, SMTP en `1025`.

Recomendación: iniciar con `docker compose up -d --build` y luego ejecutar los comandos artisan dentro de `app`.

## Variables de entorno clave

- `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER` (en desarrollo está configurado a `smtp` con `mailhog`), `MAIL_HOST=mailhog`, `MAIL_PORT=1025`

El archivo usado durante desarrollo es `.env` en la raíz del proyecto.

## Migraciones y seeders

- Ejecutar migraciones:

```bash
php artisan migrate --force
```

- Seeders principales:
  - `RolSeeder`: crea roles (`usuario`, `socio`, `administrador`).
  - `AdminAndEjemplarSeeder`: crea un admin y 100 ejemplares simples.
  - `PopulateAllSeeder`: genera 100 autores, ~200 ejemplares, 30 editoriales, 20 colecciones, 100 usuarios, wishlist y detalle_alquiler.
  - `UpdateEjemplarImagesSeeder`: corrige `image_book` para ejemplares que no tienen imagen o cuyo archivo no existe.

Ejecutar seeders:

```bash
# desde el contenedor app
composer dump-autoload
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder --force
# o ejecutar específicamente
php artisan db:seed --class=Database\\Seeders\\PopulateAllSeeder --force
php artisan db:seed --class=Database\\Seeders\\UpdateEjemplarImagesSeeder --force
```

## Emails y verificación

- En desarrollo la aplicación usa MailHog. Accede a `http://localhost:8025` para ver los correos.
- Configuración en `.env`: `MAIL_MAILER=smtp`, `MAIL_HOST=mailhog`, `MAIL_PORT=1025`.

Las notificaciones de verificación usan envío síncrono (`QUEUE_CONNECTION=sync`) por defecto, por lo que el envío se hace en el mismo proceso.

## Tests

- Ejecutar PHPUnit desde el contenedor `app`:

```bash
vendor/bin/phpunit --testdox -v
```

- Las pruebas Feature se ajustaron para evitar errores por transacciones con MySQL (`DatabaseMigrations` en lugar de `RefreshDatabase`).

## Problemas conocidos y soluciones rápidas

- SQL FK `usuario.idRol`: si al crear usuarios falla por FK, asegurarse que `rol` contiene el rol `usuario` (seed `RolSeeder`), o que `RegisterController::create()` crea el rol por defecto.
- `Data too long for column 'contenido'`: se añadió migración para cambiar `contenido` a `TEXT`.
- `mail` no llega: usamos MailHog; si prefieres SMTP real, configurar `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`.
- Warnings de dependencias (`voku/portable-ascii`): son avisos deprecación, no impiden ejecución.
- `git dubious ownership`: ejecutar dentro del contenedor `git config --global --add safe.directory /var/www/html` si Git queja.

## Comandos útiles (rápido)

```bash
# build & up
docker compose up -d --build
# entrar al contenedor app
docker compose exec -T app bash
# composer, artisan
composer install
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder --force
# compilar assets (servicio node)
docker compose exec -T node bash -lc 'npm ci && npm run production'
# ver MailHog: http://localhost:8025
```

## Archivos modificados / añadidos relevantes

- `docker-compose.yml` — añadida `mailhog`.
- `docker/php/Dockerfile` — imagen `php:8.4-fpm`.
- `app/Http/Controllers/Auth/RegisterController.php` — crea rol por defecto si falta.
- `app/Models/Usuario.php` — relaciones corregidas.
- `database/seeders/*` — nuevos seeders `PopulateAllSeeder`, `UpdateEjemplarImagesSeeder`.
- `resources/views/ejemplares/detalles.blade.php` — título del navegador ahora usa `nomEjemplar`.

## Próximos pasos recomendados

- Revisar y limpiar duplicados de Livewire si aparecen avisos de resolución ambigua.
- Completar `Reorganizar carpetas PSR-4` para estandarizar nombres y namespaces si se desea.
- Generar documentación PDF si es necesario (puedo exportarlos).

---

Si quieres, exporto este informe a PDF y lo guardo en `docs/` o lo subo a un branch y genero un commit.
