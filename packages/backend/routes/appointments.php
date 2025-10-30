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

    $response->getBody()->write(json_encode(['success' => true, 'appointment' => $appointment]));
    return $response->withHeader('Content-Type', 'application/json');
});
$group->get('/self', function ($request, $response) {
    $user = $request->getAttribute('user');
    $appointments = Appointment::where('host_user_id', $user['id'])
        ->orWhere('guest_user_id', $user['id'])
        ->get();

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
