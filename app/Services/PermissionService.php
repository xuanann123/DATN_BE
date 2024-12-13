<?php

namespace App\Services;

use App\Models\User;

class PermissionService
{
    public static function hasPermission(User $user, string $permissionSlug)
    {
        return $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('slug')->contains($permissionSlug);
    }

    public static function getAdminsWithPermission(string $permissionSlug)
    {
        return User::whereHas('roles.permissions', function ($query) use ($permissionSlug) {
            $query->where('slug', $permissionSlug);
        })->get();
    }
}
