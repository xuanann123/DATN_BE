<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super_admin có toàn quyền, không cần check
        Gate::before(function ($user, $ability) {
            if ($user->user_type === User::TYPE_SUPER_ADMIN) {
                return true;
            }
        });

        if (Schema::hasTable('permissions')) {
            $permissions = Permission::all();

            if ($permissions->isNotEmpty()) {
                // Vòng lặp quyền check xem user đó có quyền hay không ... tối ưu hệ thống
                foreach ($permissions as $permission) {
                    Gate::define($permission->slug, function ($user, $subSlug = null) use ($permission) {
                        $fullSlug = $permission->slug;
                        if ($subSlug) {
                            $fullSlug .= '.' . $subSlug;
                        }
                        return $user->hasPermission($fullSlug);
                    });
                }
            }
        } else {
            // Nếu không có bảng permissions -> vẫn định nghĩa quyền default (quyền dự phòng)
            Gate::define('default', function ($user) {
                return false;
            });
        }
    }
}
