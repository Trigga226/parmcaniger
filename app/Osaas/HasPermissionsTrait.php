<?php

namespace App\Osaas;

use App\Models\Permission;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

trait HasPermissionsTrait {
    /**
     * @return mixed
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class,'profile_user');
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'permission_user');
    }

    /**
     * @param mixed ...$profiles
     * @return bool
     */
    public function hasProfile( ... $profiles )
    {
        foreach ($profiles as $profile) {
            if ($this->profiles->contains('slug', $profile)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughProfile($permission)
    {
        foreach ($permission->profiles as $profile){
            if($this->profiles->contains($profile)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughProfile($permission) || $this->hasPermission($permission);
    }

    /**
     * @param $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {
        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }
}
