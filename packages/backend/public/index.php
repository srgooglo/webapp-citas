<?php
use Slim\Factory\AppFactory;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Appointment;

require __DIR__ . "/../bootstrap.php";

$app = AppFactory::create();

// Ejecuta la "autocreación" de la tabla
User::migrate();
Appointment::migrate();
Purchase::migrate();

$app->addBodyParsingMiddleware();

// Cargar rutas
(require __DIR__ . '/../routes/auth.php')($app);
(require __DIR__ . '/../routes/user.php')($app);
(require __DIR__ . '/../routes/appointments.php')($app);

$app->run();
