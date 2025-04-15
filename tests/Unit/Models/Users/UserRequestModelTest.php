<?php

namespace Tests\Unit\Models\Users;

use App\Models\User\UserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRequestModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_user_request()
    {
        $request = UserRequest::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('user_requests', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_it_can_update_a_user_request()
    {
        $request = UserRequest::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'status' => 'pending',
        ]);

        $request->update([
            'name' => 'Jane Smith',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('user_requests', [
            'id' => $request->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'status' => 'approved',
        ]);
    }

    public function test_it_can_delete_a_user_request()
    {
        $request = UserRequest::factory()->create();

        $request->delete();

        $this->assertDatabaseMissing('user_requests', [
            'id' => $request->id,
        ]);
    }

    public function test_status_can_be_pending_approved_or_rejected()
    {
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($statuses as $status) {
            $request = UserRequest::factory()->create(['status' => $status]);

            $this->assertDatabaseHas('user_requests', [
                'id' => $request->id,
                'status' => $status,
            ]);
        }
    }

    public function test_it_fails_when_required_fields_are_missing()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // 'name', 'email', and 'status' are all required by the fillable logic
        UserRequest::create([]);
    }

    public function test_it_accepts_duplicate_emails_but_you_can_validate_uniqueness_elsewhere()
    {
        $first = UserRequest::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $second = UserRequest::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $this->assertDatabaseCount('user_requests', 2);
        $this->assertEquals($first->email, $second->email);
    }
}
