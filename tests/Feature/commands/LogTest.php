<?php

use App\Models\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('deletes logs older than 6 months', function (): void {
    //arrange
    $oldLog = Log::factory()->create([
        'created_at' => now()->subMonths(7),
    ]);
    //act
    Artisan::call('temp:deletings-logs');
    //assert
    expect(Log::find($oldLog->id))->toBeNull();
});

it('does not delete logs created within the last 6 months', function (): void {
    //arrange
    $recentLog = Log::factory()->create([
        'created_at' => now()->subMonths(2),
    ]);
    //act
    Artisan::call('temp:deletings-logs');
    //assert
    expect(Log::find($recentLog->id))->not()->toBeNull();
});

it('displays a success message after deleting old logs', function (): void {
    //act
    Artisan::call('temp:deletings-logs');
    //assert
    expect(Artisan::output())->toContain('Logs older than 6 months have been deleted successfully.');
});
