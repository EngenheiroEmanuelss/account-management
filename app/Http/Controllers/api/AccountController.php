<?php

namespace App\Http\Controllers\api;

use App\Dto\EventData;
use App\Enum\EventTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationAccountResource;
use App\Http\Resources\OriginAccountResource;
use App\Http\Resources\TransferAccountResource;
use App\Services\AccountManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AccountController extends Controller
{
    public function __construct(protected AccountManagementService $accountManagement)
    {
        $this->service = $accountManagement;
    }

    public function reset()
    {
        $this->service->reset();

        return response('OK');
    }

    public function showBalance(Request $request)
    {
        return response($this->service->show($request->query('account_id')));
    }

    public function managementEvents(EventData $data)
    {
        return match ($data->type->value) {
            EventTypeEnum::DEPOSIT->value => $this->deposit($data),
            EventTypeEnum::TRANSFER->value => $this->transfer($data),
            EventTypeEnum::WITHDRAW->value => $this->withdraw($data),
            default => throw new \Exception(Lang::get('event.type.invalid')),
        };
    }

    private function deposit(EventData $data)
    {
        $response = $this->service->deposit($data);

        return response(new DestinationAccountResource($response), 201);
    }

    private function transfer(EventData $data)
    {
        $response = $this->service->transfer($data);

        return response(new TransferAccountResource($response), 201);
    }

    private function withdraw(EventData $data)
    {
        $response = $this->service->withdraw($data);

        return response(new OriginAccountResource($response), 201);
    }
}
