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

    public function createWithUserRole(User $user, array $data): Category
    {
        if (!$user->roles->contains('id', $data['role_id'])) {
            throw ValidationException::withMessages([
                'role_id' => 'Vous ne pouvez pas créer une catégorie pour un rôle qui ne vous est pas attribué',
            ]);
        }

        return $this->create($data);
    }

    public function updateWithUserRole(User $user, Category $category, array $data): bool
    {
        if (!$user->roles->contains('id', $data['role_id'])) {
            throw ValidationException::withMessages([
                'role_id' => 'Vous ne pouvez pas modifier cette catégorie avec ce rôle.',
            ]);
        }

        return $this->update($category, $data);
    }
}
