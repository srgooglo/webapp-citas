<?php
use Slim\App;
use App\Models\Appointment;
use App\Middlewares\JWTAuth;
use App\Models\User;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {
$app->group('/appointments', function (RouteCollectorProxy $group) {

$group->post('/create', function ($request, $response) {
    $user = $request->getAttribute('user'); // El host, autenticado
    $body = $request->getParsedBody();
    $data = User::where('name', $body['guest_user_name'])->first();
    $date = new DateTime($body['date']);
    if($data === null){
        $appointment = Appointment::create([
            'host_user_id' => $user['id'],
            'guest_user_name' => $body['guest_user_name'],
            'title' => $body['title'],
            'description' => $body['description'],
            'date' => $date,
        ]);
    }else{
        $user_guest = $data->getAttributes();
        $appointment = Appointment::create([
            'host_user_id' => $user['id'],
            'guest_user_id' => $user_guest['id'],
            'title' => $body['title'],
            'description' => $body['description'],
            'date' => $date,
        ]);
    }

    // Enviar mensaje de Telegram si el usuario tiene un telegram_id
            if (!empty($user['telegram_id'])) {
                sendTelegramMessage($user['telegram_id'], "¡Tienes una nueva cita! 📅\n\nTítulo: {$body['title']}\nDescripción: {$body['description']}\nFecha: {$body['date']}");
            }

    $response->getBody()->write(json_encode(['success' => true, 'appointment' => $appointment]));
    return $response->withHeader('Content-Type', 'application/json');
});
$group->get('/self', function ($request, $response) {
    $user = $request->getAttribute('user');
    $appointments = Appointment::with(["guestUser","hostUser"])
        ->where('host_user_id', $user['id'])
        ->orWhere('guest_user_id', $user['id'])
        ->get()
        ->map(function ($appointment) use ($request) {
            $avatarPath = $appointment->hostUser->avatar;
            if (strpos($avatarPath, 'http') === false) {
                $appointment->hostUser->avatar = $request->getUri()->getScheme() . "://"
                    . $request->getUri()->getHost()
                    . ($request->getUri()->getPort() ? ':' . $request->getUri()->getPort() : '')
                    . $avatarPath;
            }
                    // Verificar si el invitado tiene cuenta (guestUser)
                    if ($appointment->guestUser) {
                        $avatarPath = $appointment->guestUser->avatar;
                        // Verificar si el avatar existe
                        if ($avatarPath && file_exists(__DIR__ . '/../public' . $avatarPath)) {
                            $appointment->guestUser->avatar = $request->getUri()->getScheme() . "://"
                            . $request->getUri()->getHost() . ':' . $request->getUri()->getPort()
                            . $avatarPath;
                        } else {
                            $appointment->guestUser->avatar = $request->getUri()->getScheme() . "://"
                            . $request->getUri()->getHost() . ':' . $request->getUri()->getPort() . '/avatar';
                        }
                    }
                    return $appointment;
                });

    $response->getBody()->write($appointments->toJson());
    return $response->withHeader('Content-Type', 'application/json');
});
$group->get('/{id}', function ($request, $response, $args) {
    $appointment = Appointment::find($args['id']);

    if (!$appointment) {
        $response->getBody()->write(json_encode(['error' => 'Appointment not found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $response->getBody()->write(json_encode(['success' => true, 'appointment' => $appointment]));
    return $response->withHeader('Content-Type', 'application/json');
});
})->add(new JWTAuth());
};

// Función para enviar mensajes de Telegram
function sendTelegramMessage($telegramId, $message) {
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN']; // Asegúrate de agregar esto en tu archivo .env
    $apiUrl = "https://api.telegram.org/bot$botToken/sendMessage";

    $params = [
        'chat_id' => $telegramId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_exec($ch);
    curl_close($ch);
}
