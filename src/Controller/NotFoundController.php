<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class NotFoundController
 *
 * @package App\Controller
 */
class NotFoundController implements ControllerInterface
{
    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('404 File not found');

        return $response
            ->withHeader('Content-Type', 'text/plain')
            ->withStatus(404);
    }
}
