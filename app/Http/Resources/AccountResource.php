<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => (string)$this->id,
            'balance' => $this->available_balance
        ];
    }
}
