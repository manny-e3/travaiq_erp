<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apps = \App\Models\JobApplication::all();
foreach($apps as $app) {
    echo "ID: {$app->id}, Status: {$app->status}, Score: {$app->ai_score}\n";
}
