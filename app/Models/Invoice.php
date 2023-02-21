<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    const STATUS_ESTIMATE = 'DEVIS';
    const STATUS_INVOICE = 'FACTURE';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'formattedDueDate',
        'formattedInvoiceDate'
    ];


    protected $casts = [
        'total' => 'integer',
        'tax' => 'integer',
        'sub_total' => 'integer',
        'discount' => 'float',
    ];

    protected $fillable = [
        'invoice_date',
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
        'estimate_id',
        'ported',
        'ported_by',
        'ported_at',
        'total_text',
        'signed',
        'taxable',
        'created_by',
        'updated_by'
    ];

    public function getFormattedInvoiceDateAttribute()
    {
        return Carbon::parse($this->invoice_date)->format('Y-m-d');
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

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }
}
