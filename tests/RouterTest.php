<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Router;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

/**
 * Class RouterTest
 *
 * @package Tests
 */
final class RouterTest extends TestCase
{
    /** @var ServerRequestInterface */
    protected $request;

    /** @var Router */
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = (new ServerRequest())->withMethod('GET');

        $this->router = (new Router())
            ->notFound(function (): string {
                return 'not found';
            });
    }

    public function testSimpleGet(): void
    {
        $request = $this->request->withUri(new Uri('/abc?key=value'));

        $route = $this->router
            ->get('/abc', function (): string {
                return 'simple get';
            })
            ->route($request);

        $this->assertEquals('simple get', $route());
    }

    public function testSimplePost(): void
    {
        $request = $this->request
            ->withMethod('POST')
            ->withUri(new Uri('/abc?key=value'));

        $route = $this->router
            ->post('/abc', function (): string {
                return 'simple post';
            })
            ->route($request);

        $this->assertEquals('simple post', $route());
    }

    public function testPatternGet(): void
    {
        $request = $this->request->withUri(new Uri('/abc/d?key=value'));

        $route = $this->router
            ->get('/abc/[a-z0-9]+', function (): string {
                return 'pattern get';
            })
            ->route($request);

        $this->assertEquals('pattern get', $route());
    }

    public function testFileNotFoundByMethod(): void
    {
        $request = $this->request->withMethod('PUT');

        $route = $this->router->route($request);

        $this->assertEquals('not found', $route());
    }

    public function testFileNotFoundByPath(): void
    {
        $request = $this->request->withUri(new Uri('/abcz?key=value'));

        $route = $this->router->route($request);

        $this->assertEquals('not found', $route());
    }
}
