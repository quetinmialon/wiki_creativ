<?php

use App\Models\Category;
use App\Models\Role;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('category index displays categories when user is superadmin', function(): void{
    //arrange
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "superadmin"
    ]);
    $user->roles()->attach($role);
    $categories = Category::factory(5)->create();
    //act
    $this->actingAs($user);
    $response = $this->get(route('categories.index'));

    //assert
    $response->assertStatus(200);
    foreach ($categories as $category) {
        $response->assertSee($category->name);
    }
});
test('category index is not available on user that are not superadmin',function(): void{
    //assert
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "not superadmin"
    ]);
    $user->roles()->attach($role);
    //act
    $this->actingAs($user);
    $response = $this->get(route('categories.index'));

    //assert
    $response->assertStatus(403);

});

test('create categorie only display if user is superadmin', function(): void{
    //assert
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $adminRole = Role::factory()->create([
        'name' => 'superadmin'
    ]);
    $role = Role::factory()->create([
        "name"=> "not superadmin"
    ]);
    $user->roles()->attach($role);
    $admin->roles()->attach($adminRole);
    //act
    $responseNormalUser = $this->actingAs($user)
        ->get(route('categories.create'));
    $responseAdmin = $this->actingAs($admin)
        ->get(route('categories.create'));
    //assert
    $responseNormalUser->assertStatus(403);
    $responseAdmin->assertStatus(200);
});

test('edit category display a form with the category only if user is superadmin', function(): void{
    //arrange
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $adminRole = Role::factory()->create([
        'name' => 'superadmin'
    ]);
    $role = Role::factory()->create([
        "name"=> "not superadmin"
    ]);
    $user->roles()->attach($role);
    $admin->roles()->attach($adminRole);

    $category = Category::factory()->create([
        'name'=>'test'
    ]);
    //act
    $responseNormalUser = $this->actingAs($user)
        ->get(route('categories.edit',$category->id ));
    $responseAdmin = $this->actingAs($admin)
        ->get(route('categories.edit',$category->id));

    //assert
    $responseAdmin->assertStatus(200)
        ->assertSee($category->name);
    $responseNormalUser->assertStatus(403);
});

test('superadmin can create category',function(): void{
    //arrange
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "superadmin"
    ]);
    $user->roles()->attach($role);
    $expectedCategory = [
        'name' => 'test',
        'role_id' => $role->id
    ];
    //act
    $this->actingAs($user);
    $response = $this->post(route('categories.store'),$expectedCategory);
    //assert
    $response->assertStatus(302);
    $this->assertDatabaseHas('categories', [
        'name' => 'test',
        'role_id' => $role->id
    ]);
});

test('create category while user is superadmin', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "superadmin"
    ]);
    $user->roles()->attach($role);
    $this->actingAs($user);
    $response = $this->post(route('categories.store'), [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $response->assertStatus(302)
        ->assertRedirect(route('categories.index'));
    $category = Category::where('name', 'Test Category')->first();
    expect($category)->not->toBeNull();
    expect($category)->toBeInstanceOf(Category::class);
    expect($category->name)->toEqual('Test Category');
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
});

test('update category when user is superadmin', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "superadmin"
    ]);
    $user->roles()->attach($role);
    $this->actingAs($user);
    $category = Category::create([
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $response = $this->put(route('categories.update', $category), [
        'name' => 'Updated Test Category',
        'role_id' => $role->id,
    ]);
    $response->assertStatus(302)
        ->assertRedirect(route('categories.index'));
    $this->assertDatabaseMissing('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Updated Test Category',
        'role_id' => $role->id,
    ]);
});

test('delete category when user is superadmin', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "superadmin"
    ]);
    $user->roles()->attach($role);
    $this->actingAs($user);
    $category = Category::create([
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $response = $this->delete(route('categories.destroy', $category));
    $response->assertStatus(302)
        ->assertRedirect(route('categories.index'));
    $this->assertDatabaseMissing('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    expect(Category::find($category->id))->toBeNull();
});

test('create category on regular role', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "pédagogie"
    ]);
    $user->roles()->attach($role);
    $this->actingAs($user);
    $response = $this->post(route('myCategories.store'), [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $response->assertStatus(302)
        ->assertRedirect(route('myCategories.myCategories'));
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $category = Category::where('name', 'Test Category')->first();
    expect($category)->not->toBeNull();
    expect($category)->toBeInstanceOf(Category::class);
    expect($category->name)->toEqual('Test Category');
});

test('only admin can update categories', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "pédagogie"
    ]);
    $user->roles()->attach($role);
    $this->actingAs($user);
    $category = Category::create([
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $response = $this->put(route('categories.update', $category), [
        'name' => 'Updated Test Category',
        'role_id' => $role->id,
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseMissing('categories', [
        'name' => 'Updated Test Category',
        'role_id' => $role->id,
    ]);
    expect($category->name)->toEqual('Test Category');

    $AdminRole = Role::factory()->create([
        "name"=> "Admin $role->name"
    ]);
    $user->roles()->attach($AdminRole);
    $this->actingAs($user);
    $response = $this->put(route('myCategories.update', $category), [
        'name' => 'Updated Test Category',
        'role_id' => $AdminRole->id,
    ]);
    $response->assertStatus(302)
        ->assertRedirect(route('myCategories.myCategories'));
    $this->assertDatabaseMissing('categories', [
        'name' => 'Test Category',
        'role_id' => $AdminRole->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Updated Test Category',
        'role_id' => $AdminRole->id,
    ]);
});
test('only admin can delete categories', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name"=> "pédagogie"
    ]);
    $user->roles()->attach($role);
    $this->actingAs($user);
    $category = Category::create([
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $response = $this->delete(route('myCategories.destroy', $category));
    $response->assertStatus(403);
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    expect(Category::find($category->id))->not->toBeNull();
    $AdminUser = User::factory()->create();
    $AdminUser = User::find($AdminUser->id);
    // Ensure $AdminUser is an instance of User
    $AdminRole = Role::factory()->create([
        "name"=> "Admin $role->name"
    ]);
    $AdminUser->roles()->attach($AdminRole);
    $this->actingAs($AdminUser);
    $response = $this->delete(route('myCategories.destroy', $category));
    $response->assertStatus(302)
        ->assertRedirect(route('myCategories.myCategories'));
    $this->assertDatabaseMissing('categories', [
        'name' => 'Test Category',
        'role_id' => $AdminRole->id,
    ]);
    expect(Category::find($category->id))->toBeNull();
});

test('createCategoryOnUserRoles redirects guest to login', function (): void {
    $response = $this->get(route('myCategories.create'));
    $response->assertRedirect(route('login'));
});

