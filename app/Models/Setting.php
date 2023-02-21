<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'logo','name','phone','email','color','sign',
        'country_id','city','zip',
        'postal','address_one','address_two',
        'tin','rccm','invoice_prefix',
        'estimate_prefix',
    ];

    public function getLogoAttribute()
    {
        return !is_null($this->attributes['logo']) ?  storage_url('images/logos/'.$this->attributes['logo']) : '';
    }

    public function getSignAttribute()
    {
        return !is_null($this->attributes['sign']) ?  storage_url('images/signs/'.$this->attributes['sign']) : '';
    }
}
