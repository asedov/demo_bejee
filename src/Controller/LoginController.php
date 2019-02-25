<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LoginController
 *
 * @package App\Controller
 */
final class LoginController implements ControllerInterface
{
    private const ADMIN_LOGIN = 'admin';
    private const ADMIN_PASSW = 'admin';

    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $view = new View();

        if ('POST' === $request->getMethod()) {
            $post = $request->getParsedBody();
            $login = $post['login'] ?? null;
            $passw = $post['password'] ?? null;

            if (self::ADMIN_LOGIN === $login && self::ADMIN_PASSW === $passw) {
                return $response
                    ->withStatus(302)
                    ->withHeader('Location', '/')
                    ->withHeader('Set-Cookie', "login={$login}");
            }

            $view
                ->set('login', $login)
                ->set('errorMessage', 'Пользователь не найден или указан неправильный пароль!');
        }

        $html = $view->render('login');

        $response->getBody()->write($html);

        return $response;
    }
}
