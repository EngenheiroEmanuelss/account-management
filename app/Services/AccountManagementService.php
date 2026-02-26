<?php

namespace App\Services;

use App\Dto\EventData;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountManagementService
{
    protected function model(): Account
    {
        return new Account();
    }

    public function __construct()
    {
    }

    public function reset()
    {
        return $this->model()->query()->delete();
    }

    public function show(int $id)
    {
        return $this->model()
            ->where('id', $id)
            ->firstOrFail()
            ->available_balance;
    }

    public function deposit(EventData $data)
    {
        $account = $this->model()
            ->where('id', $data->destination)
            ->first();

        if ($account) {
            return DB::transaction(function () use ($account, $data) {
                return $account->increaseBalance($data->amount);
            });
        } else {
            return $this->model()
                ->create([
                    'id'                => $data->destination,
                    'available_balance' => $data->amount
                ]);
        }
    }

    public function transfer(EventData $data)
    {
        return DB::transaction(function () use ($data) {
            $originAccount = $this->model()
                ->where('id', $data->origin)
                ->lockForUpdate()
                ->firstOrFail();

            $originAccount->hasBalance($data->amount);

            $destinationAccount = $this->model()
                ->where('id', $data->destination)
                ->lockForUpdate()
                ->firstOrFail();

            return [
                'accounts' => [
                    'origin'      => $originAccount->decreaseBalance($data->amount),
                    'destination' => $destinationAccount->increaseBalance($data->amount)
                ]
            ];
        });
    }

    public function withdraw(EventData $data)
    {
        return DB::transaction(function () use ($data) {
            $account = $this->model()
                ->where('id', $data->origin)
                ->lockForUpdate()
                ->firstOrFail();

            $account->hasBalance($data->amount);

            return $account->decreaseBalance($data->amount);
        });
    }
}