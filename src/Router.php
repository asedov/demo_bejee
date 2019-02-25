<?php
declare(strict_types=1);

namespace App;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Router
 *
 * @package App
 */
final class Router
{
    /** @var array */
    private $routes;

    /** @var Closure */
    private $notFound;

    public function __construct()
    {
        $this->routes = [
            'GET'  => [],
            'POST' => [],
        ];
    }

    /**
     * @param string  $httpMethod
     * @param string  $pathPattern
     * @param Closure $closure
     * @return Router
     */
    private function addRoute(string $httpMethod, string $pathPattern, Closure $closure): Router
    {
        $this->routes[$httpMethod][$pathPattern] = $closure;

        return $this;
    }

    /**
     * @param string  $pathPattern
     * @param Closure $closure
     * @return Router
     */
    public function get(string $pathPattern, Closure $closure): Router
    {
        return $this->addRoute('GET', $pathPattern, $closure);
    }

    /**
     * @param string  $pathPattern
     * @param Closure $closure
     * @return Router
     */
    public function post(string $pathPattern, Closure $closure): Router
    {
        return $this->addRoute('POST', $pathPattern, $closure);
    }

    /**
     * @param string  $pathPattern
     * @param Closure $closure
     * @return Router
     */
    public function any(string $pathPattern, Closure $closure): Router
    {
        $this->addRoute('GET', $pathPattern, $closure);
        $this->addRoute('POST', $pathPattern, $closure);

        return $this;
    }

    /**
     * @param Closure $closure
     * @return Router
     */
    public function notFound(Closure $closure): self
    {
        $this->notFound = $closure;

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Closure
     */
    public function route(ServerRequestInterface &$request): Closure
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        if (!array_key_exists($method, $this->routes)) {
            return $this->notFound;
        }

        foreach ($this->routes[$method] as $pathPattern => $route) {
            $pattern = '@^' . $pathPattern . '$@';
            if (preg_match($pattern, $path, $matches)) {
                foreach ($matches as $name => $value) {
                    if (gettype($name) === 'string') {
                        $request = $request->withAttribute($name, $value);
                    }
                }

                return $route;
            }
        }

        return $this->notFound;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $route = $this->route($request);

        return $route($request, $response);
    }
}
