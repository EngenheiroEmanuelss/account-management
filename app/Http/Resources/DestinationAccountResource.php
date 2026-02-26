<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DestinationAccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'destination' => new AccountResource($this)
        ];
    }
}
