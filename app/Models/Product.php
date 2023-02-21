<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'image',
        'description'
    ];

    protected $casts = [
        'price' => 'double'
    ];

    protected $appends = [
        'formattedCreatedAt'
    ];

    public function getImageAttribute()
    {
        return !is_null($this->attributes['image']) ?  storage_url('images/products/'.$this->attributes['image']) : '';
    }

    public function invoices()
    {
        return $this->morphedByMany(Invoice::class, 'productable');
    }

    public function estimates()
    {
        return $this->morphedByMany(Estimate::class, 'productable');
    }

    public function scopeWhereSearch($query, $search)
    {
        return $query->where('name', 'LIKE', '%'.$search.'%');
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('price')) {
            $query->wherePrice($filters->get('price'));
        }

        if ($filters->get('unit_id')) {
            $query->whereUnit($filters->get('unit_id'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        return Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
    }
}
