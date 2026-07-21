<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Mail::raw('Test local email', function($msg) {
        $msg->to('akanderufus51@gmail.com')->subject('Test Local');
    });
    echo 'SUCCESS';
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
