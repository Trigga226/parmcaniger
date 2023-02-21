<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'type','name','manager','logo','address','phone','website','email'
    ];

    public function getLogoAttribute()
    {
        return !is_null($this->attributes['logo']) ?  storage_url('images/providers/'.$this->attributes['logo']) : '';
    }
}
