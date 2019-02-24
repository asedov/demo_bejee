<?php
declare(strict_types=1);

namespace App;

use Closure;

/**
 * Class Router
 *
 * @package App
 */
final class Router
{
    /** @var array */
    private $routes;

    public function __construct()
    {
        $this->routes = [
            'GET'      => [],
            'POST'     => [],
            'notFound' => function (): void {
                echo '404 Not found';
            },
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
        $this->routes['notFound'] = $closure;

        return $this;
    }

    /**
     * @param string $method
     * @param string $path
     * @return Closure
     */
    public function route(string $method, string $path): Closure
    {
        if (!array_key_exists($method, $this->routes)) {
            return $this->routes['notFound'];
        }

        foreach ($this->routes[$method] as $pathPattern => $route) {
            $pattern = '@^' . $pathPattern . '$@';
            if (preg_match($pattern, $path)) {
                return $route;
            }
        }

        return $this->routes['notFound'];
    }
}
