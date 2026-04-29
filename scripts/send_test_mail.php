<?php
// Bootstrap Laravel and send a test email via configured mailer
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Using MAILER=" . env('MAIL_MAILER') . " HOST=" . env('MAIL_HOST') . " PORT=" . env('MAIL_PORT') . "\n";

try {
    \Illuminate\Support\Facades\Mail::raw('This is a test message from Biblioteca para simples', function ($m) {
        $m->to('test@local.test')->subject('Biblioteca para simples - test');
    });
    echo "Mail send invoked\n";
} catch (Exception $e) {
    echo "Error sending: " . $e->getMessage() . "\n";
}
