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


    // event, tipo de evento (Enum) com Match, chamando corretamente a opção.
// conta, account_id, numero, saldo disponível, increase, decrease, disponível.
// criar DTO para validar a request.
// definir rotas corretas para tudo
// criar Resources para os retornos corretos, um para conta.
// usar laravel data para retornar.
// usar lock for update.

// métodos:
// salvar, salva conta com saldo
// transferir, transferir valor de uma conta para outra
// depositar, adicionar saldo

// casos de teste:
// saldo menor ou igual a zero, valores inválidos
// conta inexistente em ambos os casos
// saldo insuficiente em conta
// deixar métodos do controller privados e apenas o principal público
// criar uma camada de service

}