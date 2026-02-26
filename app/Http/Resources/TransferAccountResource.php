<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferAccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'origin'      => new AccountResource($this['accounts']['origin']),
            'destination' => new AccountResource($this['accounts']['destination']),
        ];
    }
}
