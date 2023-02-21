<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EstimateCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function ($estimate){
                return [
                    'id' => $estimate->id,
                    'estimate_date' => $estimate->estimate_date,
                    'due_date' => $estimate->due_date,
                    'reference' => $estimate->reference,
                    'user_id' => $estimate->user_id,
                    'status' => $estimate->status,
                    'sub_total' => $estimate->sub_total,
                    'total' => $estimate->total,
                    'discount' => $estimate->discount,
                    'due_amount' => $estimate->due_amount,
                    'tax' => $estimate->tax,
                    'notes' => $estimate->notes,
                    'hash' => $estimate->hash,
                    'ported' => $estimate->ported,
                    'ported_by' => $estimate->ported_by,
                    'ported_at' => $estimate->ported_at,
                    'total_text' => $estimate->total_text,
                    'taxable' => $estimate->taxable,
                    'signed' => $estimate->signed,
                    'created_by' => $estimate->created_by,
                    'updated_by' => $estimate->updated_by,
                    'client' => $estimate->client
                ];
            })
        ];
    }
}
