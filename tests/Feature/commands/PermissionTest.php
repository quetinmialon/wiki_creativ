<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('deletes denied permissions older than one month', function () {
    $oldDenied = Permission::factory()->create([
        'status' => 'denied',
        'created_at' => now()->subMonths(2),
    ]);

    Artisan::call('temp:deleting-expired-permissions');

    expect(Permission::find($oldDenied->id))->toBeNull();
});

test('deletes expired permissions older than one month', function () {
    $oldExpired = Permission::factory()->create([
        'status' => 'expired',
        'created_at' => now()->subMonths(2),
    ]);

    Artisan::call('temp:deleting-expired-permissions');

    expect(Permission::find($oldExpired->id))->toBeNull();
});

test('does not delete denied permissions created less than a month ago', function () {
    $recentDenied = Permission::factory()->create([
        'status' => 'denied',
        'created_at' => now()->subDays(10),
    ]);

    Artisan::call('temp:deleting-expired-permissions');

    expect(Permission::find($recentDenied->id))->not()->toBeNull();
});

test('does not delete approved permissions regardless of age', function () {
    $oldApproved = Permission::factory()->create([
        'status' => 'approved',
        'created_at' => now()->subMonths(3),
    ]);

    Artisan::call('temp:deleting-expired-permissions');

    expect(Permission::find($oldApproved->id))->not()->toBeNull();
});
test('does not delete pending permissions regardless of age', function () {
    $oldPending = Permission::factory()->create([
        'status' => 'pending',
        'created_at' => now()->subMonths(3),
    ]);

    Artisan::call('temp:deleting-expired-permissions');

    expect(Permission::find($oldPending->id))->not()->toBeNull();
});

test('displays a success message when finished', function () {
    Artisan::call('temp:deleting-expired-permissions');

    expect(Artisan::output())->toContain('Expired and denied permissions have been deleted successfully.');
});

test('updates status to expired for approved permissions wtesth expired_at in the past', function () {
    $approved = Permission::factory()->create([
        'status' => 'approved',
        'expired_at' => now()->subDays(1),
    ]);

    Artisan::call('temp:update-expired-permissions');

    expect(Permission::find($approved->id)->status)->toBe('expired');
});

test('does not change status of approved permissions wtesth expired_at in the future', function () {
    $approvedFuture = Permission::factory()->create([
        'status' => 'approved',
        'expired_at' => now()->addDays(5),
    ]);

    Artisan::call('temp:update-expired-permissions');

    expect(Permission::find($approvedFuture->id)->status)->toBe('approved');
});

test('does not change status of permissions that are not approved, regardless of expired_at', function () {
    $denied = Permission::factory()->create([
        'status' => 'denied',
        'expired_at' => now()->subDays(10),
    ]);

    $pending = Permission::factory()->create([
        'status' => 'pending',
        'expired_at' => now()->subDays(10),
    ]);

    Artisan::call('temp:update-expired-permissions');

    expect(Permission::find($denied->id)->status)->toBe('denied');
    expect(Permission::find($pending->id)->status)->toBe('pending');
});

test('displays a success message after updating permissions', function () {
    Artisan::call('temp:update-expired-permissions');

    expect(Artisan::output())->toContain('Expired permissions have been updated successfully.');
});