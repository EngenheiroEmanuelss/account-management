<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'available_balance'
    ];

    public function increaseBalance(int $amount)
    {
        $this->available_balance += $amount;
        $this->save();

        return $this;
    }

    public function decreaseBalance(int $amount)
    {
        $this->available_balance -= $amount;
        $this->save();

        return $this;
    }

    public function hasBalance(int $amount)
    {
        if ($this->available_balance < $amount) {
            throw ValidationException::withMessages([
                'account' => Lang::get('account.balance.insufficient')
            ]);
        }

        return true;
    }
}
