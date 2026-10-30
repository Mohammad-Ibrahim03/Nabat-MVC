<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/app/Core/Store.php';

ini_set('display_errors', '0');

$route = strtolower(trim((string) ($_GET['route'] ?? 'home')));
if ($route === '') $route = 'home';

/** Connect only on routes that need MySQL, so the marketing pages can still render. */
function nabta_db_or_error(): PDO
{
    try {
        return Database::connection();
    } catch (Throwable $exception) {
        error_log('Nabta database connection error: ' . $exception->getMessage());
        http_response_code(503);
        require dirname(__DIR__) . '/app/Views/database-error.php';
        exit;
    }
}

switch ($route) {
    case 'home':
        require dirname(__DIR__) . '/app/Views/home.php';
        break;

    case 'about':
        require dirname(__DIR__) . '/app/Views/about/index.php';
        break;

    case 'login':
    case 'register':
        require dirname(__DIR__) . '/app/Controllers/AuthController.php';
        $auth = new AuthController(nabta_db_or_error());
        $route === 'login' ? $auth->login() : $auth->register();
        break;

    case 'logout':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            http_response_code(405);
            header('Allow: POST');
            exit('This action could not be completed. Return to Nabta and try again.');
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
        header('Location: index.php');
        exit;

    case 'google-auth':
        require dirname(__DIR__) . '/app/Controllers/GoogleAuthController.php';
        try {
            (new GoogleAuthController(Database::connection()))->authenticate();
        } catch (Throwable $exception) {
            error_log('Nabta Google authentication error: ' . $exception->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, private');
            http_response_code(503);
            echo json_encode(['ok' => false, 'message' => 'Nabta could not reach the account database. Please try again in a moment.'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'products':
        require dirname(__DIR__) . '/app/Controllers/ProductController.php';
        (new ProductController(nabta_db_or_error()))->index();
        break;

    case 'product':
        require dirname(__DIR__) . '/app/Controllers/ProductController.php';
        (new ProductController(nabta_db_or_error()))->show();
        break;

    case 'cart':
        require dirname(__DIR__) . '/app/Controllers/CartController.php';
        $cart = new CartController(nabta_db_or_error());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $cart->handlePost();
        $cart->index();
        break;

    case 'checkout':
        require dirname(__DIR__) . '/app/Controllers/OrderController.php';
        (new OrderController(nabta_db_or_error()))->checkout();
        break;

    case 'orders':
        require dirname(__DIR__) . '/app/Controllers/OrderController.php';
        (new OrderController(nabta_db_or_error()))->history();
        break;

    case 'profile':
        require dirname(__DIR__) . '/app/Controllers/ProfileController.php';
        (new ProfileController(nabta_db_or_error()))->index();
        break;

    case 'wishlist':
        require dirname(__DIR__) . '/app/Controllers/WishlistController.php';
        (new WishlistController(nabta_db_or_error()))->index();
        break;

    case 'wishlist-action':
        require dirname(__DIR__) . '/app/Controllers/WishlistController.php';
        (new WishlistController(nabta_db_or_error()))->handlePost();
        break;

    case 'contact':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php#contact-us');
            exit;
        }
        require dirname(__DIR__) . '/app/Controllers/ContactController.php';
        (new ContactController(nabta_db_or_error()))->submit();
        break;

    case 'assistant':
        $pageTitle = 'Find your plant';
        require dirname(__DIR__) . '/app/Views/assistant.php';
        break;

    case 'sell':
    case 'my-listings':
        $pageTitle = 'Grow with Nabta';
        require dirname(__DIR__) . '/app/Views/sell.php';
        break;

    case 'admin':
        require dirname(__DIR__) . '/app/Controllers/AdminController.php';
        (new AdminController(nabta_db_or_error()))->dashboard();
        break;

    default:
        http_response_code(404);
        $pageTitle = 'Page not found';
        require dirname(__DIR__) . '/app/Views/404.php';
        break;
}
