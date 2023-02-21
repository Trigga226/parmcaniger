<?php

namespace App\Http\Resources\Osaas;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProfilesCollection extends ResourceCollection
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
            'data' => $this->collection->transform(function ($profile){
                return [
                    'id' => $profile->id,
                    'name' => $profile->name,
                    'permissions' => $profile->permissions->pluck('id'),
                ];
            })
        ];
    }
}
