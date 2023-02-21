<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'phone','address','logo','email','website'
    ];

    /**
     * @return mixed
     */
    public function getLogoAttribute()
    {
        return !is_null($this->attributes['logo']) ?  storage_url('images/clients/'.$this->attributes['logo']) : '';
    }
}
