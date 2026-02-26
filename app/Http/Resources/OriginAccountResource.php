<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OriginAccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'origin' => new AccountResource($this)
        ];
    }
}
