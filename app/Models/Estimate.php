<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    const STATUS_ESTIMATE = 'DEVIS';
    const STATUS_INVOICE = 'FACTURE';

    protected $appends = [
        'formattedDueDate',
        'formattedEstimateDate'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'total' => 'integer',
        'tax' => 'integer',
        'sub_total' => 'integer',
        'discount' => 'float',
    ];

    protected $fillable = [
        'estimate_date',
        'due_date',
        'reference',
        'user_id',
        'status',
        'sub_total',
        'total',
        'discount',
        'due_amount',
        'tax',
        'notes',
        'hash',
        'ported',
        'ported_by',
        'ported_at',
        'total_text',
        'taxable',
        'signed',
        'created_by',
        'updated_by'
    ];

    public function getFormattedEstimateDateAttribute()
    {
        return Carbon::parse($this->estimate_date)->format('Y-m-d');
    }

    public function getFormattedDueDateAttribute($value)
    {
        return Carbon::parse($this->due_date)->format('Y-m-d');
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')->withPivot('quantity','name','price','description','discount','total');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
