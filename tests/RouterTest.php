<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Router;

/**
 * Class RouterTest
 */
final class RouterTest extends TestCase
{
    /** @var Router */
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = (new Router())
            ->notFound(function (): string {
                return 'not found';
            });
    }

    public function testSimpleGet(): void
    {
        $route = $this->router
            ->get('/abc', function (): string {
                return 'simple get';
            })
            ->route('GET', '/abc');

        $this->assertEquals('simple get', $route());
    }

    public function testSimplePost(): void
    {
        $route = $this->router
            ->post('/abc', function (): string {
                return 'simple post';
            })
            ->route('POST', '/abc');
        $this->assertEquals('simple post', $route());
    }

    public function testPatternGet(): void
    {
        $route = $this->router
            ->get('/abc/[a-z0-9]+', function (): string {
                return 'pattern get';
            })
            ->route('GET', '/abc/d');
        $this->assertEquals('pattern get', $route());
    }

    public function testFileNotFoundByMethod(): void
    {
        $route = $this->router->route('PUT', '/abc');
        $this->assertEquals('not found', $route());
    }

    public function testFileNotFoundByPath(): void
    {
        $route = $this->router->route('GET', '/abcz');
        $this->assertEquals('not found', $route());
    }
}
