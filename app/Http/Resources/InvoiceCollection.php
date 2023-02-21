<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function ($invoice){
                return [
                    'id' => $invoice->id,
                    'invoice_date' => $invoice->invoice_date,
                    'due_date' => $invoice->due_date,
                    'reference' => $invoice->reference,
                    'user_id' => $invoice->user_id,
                    'status' => $invoice->status,
                    'sub_total' => $invoice->sub_total,
                    'total' => $invoice->total,
                    'discount' => $invoice->discount,
                    'due_amount' => $invoice->due_amount,
                    'tax' => $invoice->tax,
                    'notes' => $invoice->notes,
                    'hash' => $invoice->hash,
                    'ported' => $invoice->ported,
                    'ported_by' => $invoice->ported_by,
                    'ported_at' => $invoice->ported_at,
                    'total_text' => $invoice->total_text,
                    'taxable' => $invoice->taxable,
                    'signed' => $invoice->signed,
                    'created_by' => $invoice->created_by,
                    'updated_by' => $invoice->updated_by,
                    'client' => $invoice->client,
                    'estimate' => $invoice->estimate
                ];
            })
        ];
    }
}
