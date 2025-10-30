<?php
use Slim\App;
use App\Models\User;
use Firebase\JWT\JWT;

return function (App $app) {
    $app->post('/auth/register', function ($request, $response) {
        $data = $request->getParsedBody();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->pwd_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->save();

        $payload = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        $response->getBody()->write(json_encode(['token' => $jwt]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/auth', function ($request, $response) {
        $data = $request->getParsedBody();
        $user = User::where('email', $data['email'])->first();
        if (!$user || !password_verify($data['password'], $user->pwd_hash)) {
            $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');

        }

        $token = JWT::encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 3600
        ], $_ENV['JWT_SECRET'], 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
