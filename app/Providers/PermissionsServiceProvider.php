<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\{Gate, Blade, Schema};
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(Schema::hasTable('permissions')){
            Permission::get()->map(function($permission){
                Gate::define($permission->slug, function($user) use ($permission){
                    return $user->hasPermissionTo($permission);
                });
            });
        }

        Blade::directive('profile', function ($profile){
            return "<?php if(auth()->check() && auth()->user()->hasProfile({$profile})) :";
        });

        Blade::directive('endprofile', function (){
            return "<?php endif; ?>";
        });
    }
}
