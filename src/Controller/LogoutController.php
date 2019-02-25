<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class LogoutController
 *
 * @package App\Controller
 */
final class LogoutController implements ControllerInterface
{
    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withStatus(302)
            ->withHeader('Set-Cookie', 'login=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0')
            ->withHeader('Location', '/');
    }
}
