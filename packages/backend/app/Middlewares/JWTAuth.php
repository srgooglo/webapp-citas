<?php
namespace App\Middlewares;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JWTAuth
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $response = new \Slim\Psr7\Response();

        //verificar si viene el token en el header
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $response->getBody()->write(json_encode(['error' => 'Missing token']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = substr($authHeader, 7);

        //intentamos decodificar el token
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            //convertir de stdClass a array
            $decoded = json_decode(json_encode($decoded), true);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }


        //con la informacion decodificada, verificar si el usuario existe
        $data = User::where('id', $decoded['user_id'])->first();
        if ($data === null) {
            $response->getBody()->write(json_encode(['error' => "User not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $user = $data->getAttributes();

        if ($user['avatar'] === null || !file_exists("../public" . $user['avatar'])) {
            $user['avatar'] = $request->getUri()->getScheme() . "://" . $request->getUri()->getHost() . ':' . $request->getUri()->getPort() . '/avatar';
        }else{
            $user['avatar'] = $request->getUri()->getScheme() . "://" . $request->getUri()->getHost() . ':' . $request->getUri()->getPort() . $user['avatar'];
        }
    	$request = $request
                 ->withAttribute('user', $user);
     return $handler->handle($request);

    }
}
