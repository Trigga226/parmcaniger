<?php

namespace App\Models;

use App\Osaas\HasPermissionsTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone','status','parent_id',
        'avatar', 'email_verified_at', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return mixed
     */
    public function getAvatarAttribute()
    {
        return !is_null($this->attributes['avatar']) ?  storage_url($this->attributes['avatar']) : '/img/profile_av.jpg';
    }

    /**
     * Automatically hash the password.
     *
     * @param $password
     */
    protected function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeIndex($query)
    {
        return $query->whereNotNull('parent_id')->with(['permissions','profiles'])->orderBy('created_at','DESC')->paginate(10);
    }

    /**
     * @param $query
     * @param $field
     * @param $value
     * @return mixed
     */
    public  function scopeSearch($query,$field,$value)
    {
        return $query->whereNotNull('parent_id')->where($field,'LIKE',"%$value%")->orderBy('id','DESC')->paginate(10);
    }
}
