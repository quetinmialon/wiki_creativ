<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\FavoriteController;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    protected $favoriteService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->favoriteService = Mockery::mock(FavoriteService::class);
        $this->controller = new FavoriteController($this->favoriteService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_toggle_favorite_returns_401_if_user_not_authenticated()
    {
        Auth::shouldReceive('id')->andReturn(null);

        $request = Request::create('/favorites/toggle/1', 'POST');
        $response = $this->controller->toggleFavorite($request, 1);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Utilisateur non authentifié', $response->getData()->message);
    }

    public function test_toggle_favorite_removes_document_if_already_favorited()
    {
        Auth::shouldReceive('id')->andReturn(42);

        $this->favoriteService
            ->shouldReceive('isFavorited')
            ->once()
            ->with(5, 42)
            ->andReturn(true);

        $this->favoriteService
            ->shouldReceive('removeFromFavorites')
            ->once()
            ->with(5, 42);

        $request = Request::create('/favorites/toggle/5', 'POST');
        $response = $this->controller->toggleFavorite($request, 5);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Retiré des favoris avec succès', $response->getData()->message);
        $this->assertFalse($response->getData()->favorited);
    }

    public function test_toggle_favorite_adds_document_if_not_yet_favorited()
    {
        Auth::shouldReceive('id')->andReturn(42);

        $this->favoriteService
            ->shouldReceive('isFavorited')
            ->once()
            ->with(10, 42)
            ->andReturn(false);

        $this->favoriteService
            ->shouldReceive('addToFavorites')
            ->once()
            ->with(10, 42);

        $request = Request::create('/favorites/toggle/10', 'POST');
        $response = $this->controller->toggleFavorite($request, 10);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Ajouté aux favoris avec succès', $response->getData()->message);
        $this->assertTrue($response->getData()->favorited);
    }
}
