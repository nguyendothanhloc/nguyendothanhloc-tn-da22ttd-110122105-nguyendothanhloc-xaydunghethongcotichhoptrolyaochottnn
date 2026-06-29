<?php

namespace Tests\Unit;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that middleware allows access when user has correct role.
     */
    public function test_middleware_allows_access_with_correct_role(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn() => response('OK'), 'admin');

        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Test that middleware blocks access when user has incorrect role.
     */
    public function test_middleware_blocks_access_with_incorrect_role(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Unauthorized action.');

        $user = User::factory()->create(['role' => 'student']);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new RoleMiddleware();
        $middleware->handle($request, fn() => response('OK'), 'admin');
    }

    /**
     * Test that middleware allows access when user has one of multiple allowed roles.
     */
    public function test_middleware_allows_access_with_one_of_multiple_roles(): void
    {
        $user = User::factory()->create(['role' => 'teacher']);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn() => response('OK'), 'admin', 'teacher');

        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Test that middleware redirects to login when user is not authenticated.
     */
    public function test_middleware_redirects_to_login_when_not_authenticated(): void
    {
        Route::get('/login', fn() => 'Login Page')->name('login');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => null);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn() => response('OK'), 'admin');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue(str_contains($response->headers->get('Location'), 'login'));
    }
}
