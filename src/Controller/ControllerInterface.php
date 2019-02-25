<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ControllerInterface
 *
 * @package App\Controller
 */
interface ControllerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
