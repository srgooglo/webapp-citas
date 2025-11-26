<?php
use Slim\App;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Slim\Routing\RouteCollectorProxy;
use App\Models\User;
use App\Models\Purchase;
use App\Middlewares\JWTAuth;

return function (App $app) {
$app->group('/purchase', function (RouteCollectorProxy $group) {

$group->post('/create-checkout-session', function ($request, $response) {
    $user = $request->getAttribute('user'); // Usuario autenticado

    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    // Registrar la compra en la base de datos
    $purchase = Purchase::create([
        'user_id' => $user['id'],
        'amount' => 6.00, // 6 EUR
        'status' => 'pending',
    ]);

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Premium Membership',
                ],
                'unit_amount' => 600, // 6.00 EUR
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $_ENV['APP_URL'] . '/purchase/payment-success?session_id={CHECKOUT_SESSION_ID}&purchase_id=' . $purchase->id . '&user_id=' . $user['id']   ,
        'cancel_url' => $_ENV['APP_URL'] . '/purchase/payment-cancel?purchase_id=' . $purchase->id,
    ]);

    $response->getBody()->write(json_encode([
        'url' => $session->url,
    ]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
})->add(new JWTAuth());

// Ruta para confirmar la compra
$group->get('/payment-success', function ($request, $response) {
    $session_id = $request->getQueryParams()['session_id'];
    $purchase_id = $request->getQueryParams()['purchase_id'];
    $user_id = $request->getQueryParams()['user_id'];

    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    $session = Session::retrieve($session_id);
    $user = $request->getAttribute('user');

    if ($session->payment_status === 'paid') {
        // Actualizar el estado de la compra
        $purchase = Purchase::find($purchase_id);
        $purchase->status = 'completed';
        $purchase->save();

        // Hacer al usuario premium
        $userModel = User::find($user_id);
        $userModel->is_premium = true;
        $userModel->save();
    }
            header("Location: http://localhost:5173");
            exit;
});

// Ruta para cancelar la compra
$group->get('/payment-cancel', function ($request, $response) {
    $purchase_id = $request->getQueryParams()['purchase_id'];

    // Marcar la compra como fallida
    $purchase = Purchase::find($purchase_id);
    if ($purchase) {
        $purchase->status = 'failed';
        $purchase->save();
    }

    header("Location: http://localhost:5173");
    exit;
});

});
};
