<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'expense_date',
        'notes',
        'attachment_receipt'
    ];

    protected $appends = [
        'formattedExpenseDate',
        'formattedCreatedAt',
        'receiptUrl'
    ];

    public function getFormattedExpenseDateAttribute($value)
    {
        return Carbon::parse($this->expense_date)->format('Y-m-d');
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function getReceiptUrlAttribute($value)
    {
        return !is_null($this->attributes['attachment_receipt']) ?  storage_url('others/receipt/'.$this->attributes['attachment_receipt']) : '';
    }
}
