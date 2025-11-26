<?php
use Slim\App;
use App\Models\Appointment;
use App\Middlewares\JWTAuth;
use App\Models\User;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {
$app->group('/appointments', function (RouteCollectorProxy $group) {

$group->post('', function ($request, $response) {
    $user = $request->getAttribute('user'); // El host, autenticado
    $body = $request->getParsedBody();
    $data = User::where('name', $body['guest_user_name'])->first();
    $date = new DateTime($body['date']);
    if($data === null){
        $appointment = Appointment::create([
            'host_user_id' => $user['id'],
            'guest_user_name' => $body['guest_user_name'],
            'guest_user_telegram_id' => $body['guest_user_telegram_id'],
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
            if (!empty($body['guest_user_telegram_id'])) {
                sendTelegramMessage($body['guest_user_telegram_id'], "¡Tienes una nueva cita con {$user['name']}! 📅\n\nTítulo: {$body['title']}\nDescripción: {$body['description']}\nFecha: {$body['date']}");
            } else {
                // Invitado con cuenta
                    $guest = User::find($appointment->guest_user_id);
                    if ($guest && !empty($guest->telegram_id)) {
                        sendTelegramMessage(
                            $guest->telegram_id,
                            "¡Tienes una nueva cita con {$user['name']}! 📅\n\nTítulo: {$body['title']}\nDescripción: {$body['description']}\nFecha: {$body['date']}"
                        );
                    }
            }

    $response->getBody()->write(json_encode(['success' => true, 'appointment' => $appointment]));
    return $response->withHeader('Content-Type', 'application/json');
});
$group->get('', function ($request, $response) {
    $user = $request->getAttribute('user');
    $baseUrl = $request->getUri()->getScheme() . "://"
    . $request->getUri()->getHost()
    . ($request->getUri()->getPort() ? ':' . $request->getUri()->getPort() : '');

    $appointments = Appointment::with(["guestUser","hostUser"])
        ->where('host_user_id', $user['id'])
        ->orWhere('guest_user_id', $user['id'])
        ->get()
        ->map(function ($appointment) use ($baseUrl) {
                    // Avatar del host
                    if ($appointment->hostUser) {
                        $hostAvatar = $appointment->hostUser->avatar;
                        $appointment->hostUser->avatar = $hostAvatar
                            ? (strpos($hostAvatar, 'http') === false ? $baseUrl . $hostAvatar : $hostAvatar)
                            : $baseUrl . '/avatar';
                    }

                    // Avatar del invitado
                    if ($appointment->guestUser) {
                        $guestAvatar = $appointment->guestUser->avatar;
                        $appointment->guestUser->avatar = $guestAvatar
                            ? (strpos($guestAvatar, 'http') === false ? $baseUrl . $guestAvatar : $guestAvatar)
                            : $baseUrl . '/avatar';
                    }

                    return $appointment;
                });

    $response->getBody()->write($appointments->toJson());
    return $response->withHeader('Content-Type', 'application/json');
});
$group->put('', function ($request, $response) {
    $user = $request->getAttribute('user'); // El host, autenticado
    $body = $request->getParsedBody();
    $data = User::where('name', $body['guest_user_name'])->first();
    $date = new DateTime($body['date']);
    if($data === null){
        $appointment = Appointment::find($body['id']);
        $appointment->guest_user_name = $body['guest_user_name'];
        $appointment->title = $body['title'];
        $appointment->description = $body['description'];
        $appointment->date = $date;
        $appointment->save();
    }else{
        $user_guest = $data->getAttributes();
        $appointment = Appointment::find($body['id']);
        $appointment->guest_user_id = $user_guest['id'];
        $appointment->title = $body['title'];
        $appointment->description = $body['description'];
        $appointment->date = $date;
        $appointment->save();
    }

    // Enviar mensaje de Telegram si el usuario tiene un telegram_id
            if (!empty($user['telegram_id'])) {
                sendTelegramMessage($user['telegram_id'], "¡Has editado tu cita! 📅\n\nTítulo: {$body['title']}\nDescripción: {$body['description']}\nFecha: {$body['date']}");
            }
            if (!empty($appointment->guest_user_telegram_id)) {
                sendTelegramMessage($appointment->guest_user_telegram_id, "¡La cita de {$user['name']} con usted ha sido editada! 📅\n\nTítulo: {$body['title']}\nDescripción: {$body['description']}\nFecha: {$body['date']}");
            } else {
                // Invitado con cuenta
                    $guest = User::find($appointment->guest_user_id);
                    if ($guest && !empty($guest->telegram_id)) {
                        sendTelegramMessage(
                            $guest->telegram_id,
                            "¡La cita de {$user['name']} con usted ha sido editada! 📅\n\nTítulo: {$body['title']}\nDescripción: {$body['description']}\nFecha: {$body['date']}"
                        );
                    }
            }

    $response->getBody()->write(json_encode(['success' => true, 'appointment' => $appointment]));
    return $response->withHeader('Content-Type', 'application/json');
});
$group->delete('', function ($request, $response) {
    $user = $request->getAttribute('user');
    $body = $request->getParsedBody();
    echo $body['id'];

    // Verificar que la cita existe y pertenece al usuario
    $appointment = Appointment::find($body['id']);

    $appointment->delete();

    // Enviar mensaje de Telegram si aplica
    if (!empty($user['telegram_id'])) {
        sendTelegramMessage($user['telegram_id'], "Has eliminado la cita: {$appointment->title} 📅");
    }

    if (!empty($appointment->guest_user_telegram_id)) {
        // Invitado sin cuenta pero con telegram
        sendTelegramMessage($appointment->guest_user_telegram_id, "{$user['name']} ha cancelado la cita: {$appointment->title} 📅");
    } else {
        // Invitado con cuenta
            $guest = User::find($appointment->guest_user_id);
            if ($guest && !empty($guest->telegram_id)) {
                sendTelegramMessage(
                    $guest->telegram_id,
                    "{$user['name']} ha cancelado la cita: {$appointment->title} 📅"
                );
            }
    }

    $response->getBody()->write(json_encode(['success' => true]));
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
