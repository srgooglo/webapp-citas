<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Models\User;
use App\Middlewares\JWTAuth;

return function (App $app) {

    $app->group('/users', function (RouteCollectorProxy $group) {

        $group->get("/self", function ($request, $response){
            $user = $request->getAttribute('user');
                $response->getBody()->write(json_encode($user));
                return $response->withHeader('Content-Type', 'application/json');
        });
        $group->post('/self/avatar', function ($request, $response){
            $user = $request->getAttribute('user');
                $user_id = $user['id'];

                // Ruta destino (asegúrate de que la carpeta "uploads" exista y tenga permisos)
                $uploadDir = __DIR__ . '/../public/uploads';
                $filename = "avatar" . $user_id;
                $path = $uploadDir . '/' . $filename;

                $uploadedFiles = $request->getUploadedFiles();

                    if (!isset($uploadedFiles['file'])) {
                        $response->getBody()->write(json_encode(['error' => 'No file uploaded']));
                        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                    }

                    $file = $uploadedFiles['file'];

                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        $response->getBody()->write(json_encode([
                                'error' => 'Error uploading file',
                                'code' => $file->getError()
                            ]));
                            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                    }
                    // Guardar en el sistema de archivos
                    $file->moveTo($path);
                    // Obtener datos del usuario autenticado

                        $user = User::find($user_id);

                        if (!$user) {
                            $response->getBody()->write(json_encode(['error' => 'User not found']));
                            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                        }

                    $user->avatar = '/uploads/' . $filename;
                    $user->save();

                    $response->getBody()->write(json_encode([
                        'success' => true,
                        'filename' => $filename
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        });
    })->add(new JWTAuth());
};
