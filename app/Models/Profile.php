<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * @param $query
     * @return mixed
     */
    public function scopeIndex($query)
    {
        return $query->with('permissions')->orderBy('created_at','DESC')->paginate(10);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany(Permission::class,'permission_profile');
    }
}
