<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\AuthController;
use App\Services\AuthService;

class AuthControllerTest extends TestCase
{
    protected $authService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthService::class);
        $this->controller = new AuthController($this->authService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    public function test_showLoginForm_returns_view()
    {
        $response = $this->controller->showLoginForm();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('auth.login', $response->getName());
    }
    public function test_login_successful_redirects_to_intended()
    {
        $request = Request::create('/', 'POST', [
            'email' => 'test@example.com',
            'password' => 'secret'
        ]);

        $this->authService
            ->shouldReceive('login')
            ->with([
                'email' => 'test@example.com',
                'password' => 'secret'
            ])
            ->once()
            ->andReturn(true);

        $response = $this->controller->login($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('/'), $response->getTargetUrl());
    }
    public function test_login_fails_with_validation_exception()
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/', 'POST', [
            'email' => 'invalid-email',
            'password' => ''
        ]);

        $this->authService
            ->shouldReceive('login')
            ->andThrow(ValidationException::withMessages(['auth.failed']));

        $this->controller->login($request);
    }
    public function test_logout_redirects_to_home()
    {
        $request = Request::create('/', 'POST');

        $this->authService
            ->shouldReceive('logout')
            ->once()
            ->with($request);

        $response = $this->controller->logout($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('/'), $response->getTargetUrl());
    }
    public function test_showForgotPasswordForm_returns_view()
    {
        $response = $this->controller->showForgotPasswordForm();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('auth.forgot-password', $response->getName());
    }

    public function test_show_reset_form_passes_token_and_email()
    {
        $request = Request::create('/', 'GET', [
            'email' => 'test@example.com'
        ]);

        $response = $this->controller->showResetForm($request, '123TOKEN');

        $this->assertEquals('auth.reset-password', $response->name());
        $this->assertEquals('123TOKEN', $response->getData()['token']);
        $this->assertEquals('test@example.com', $response->getData()['email']);
    }
    public function test_reset_success_redirects_to_login()
    {
        $request = Request::create('/', 'POST', [
            'email' => 'test@example.com',
            'password' => 'new-password',
            'token' => 'valid-token'
        ]);

        $this->authService
            ->shouldReceive('resetPassword')
            ->with([
                'email' => 'test@example.com',
                'password' => 'new-password',
                'token' => 'valid-token'
            ])
            ->once()
            ->andReturn(Password::PASSWORD_RESET);

        $response = $this->controller->reset($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertEquals('Password reset successful.', session('status'));
    }
    public function test_reset_failure_returns_back_with_errors()
    {
        $request = Request::create('/', 'POST', [
            'email' => 'test@example.com',
            'password' => 'new-password',
            'token' => 'invalid-token'
        ]);

        $this->authService
            ->shouldReceive('resetPassword')
            ->with([
                'email' => 'test@example.com',
                'password' => 'new-password',
                'token' => 'invalid-token'
            ])
            ->once()
            ->andReturn('passwords.token');

        $response = $this->controller->reset($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertArrayHasKey('email', session('errors')->getBag('default')->toArray());
    }
    public function test_reset_validation_fails()
    {
        $this->expectException(ValidationException::class);

        $request = Request::create('/', 'POST', [
            'email' => 'invalid-email',
            'password' => '',
            'token' => ''
        ]);

        $this->controller->reset($request);
    }
}
