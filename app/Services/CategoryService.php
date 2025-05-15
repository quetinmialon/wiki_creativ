<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function getAll(): Collection
    {
        return Category::all();
    }

    public function getUserCategories(User $user): Collection
    {
        return Category::whereIn('role_id', $user->roles->pluck('id'))->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function getById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function getRolesForUser(User $user): array
    {
        return $user->roles->all();
    }
}
