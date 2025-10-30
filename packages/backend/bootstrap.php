<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$capsule = new Capsule();

$capsule->addConnection([
    "driver" => "mysql",
    "host" => "127.0.0.1",
    "database" => "webapp_citas",
    "username" => "user",
    "password" => "password"
]);

$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$capsule->bootEloquent();
