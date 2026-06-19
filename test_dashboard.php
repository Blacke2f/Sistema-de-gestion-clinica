<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\Usuario::first();
if ($user) {
    Auth::login($user);
}

$req = Illuminate\Http\Request::create('/', 'GET');
$res = $kernel->handle($req);
echo 'STATUS: ' . $res->getStatusCode() . PHP_EOL;
if ($res->getStatusCode() >= 400) {
    echo 'CONTENT: ' . substr(strip_tags($res->getContent()), 0, 500) . PHP_EOL;
}
