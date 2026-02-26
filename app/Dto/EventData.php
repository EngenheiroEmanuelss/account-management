<?php

namespace App\Dto;

use App\Enum\EventTypeEnum;
use Illuminate\Http\Request;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class EventData extends Data
{
    public function __construct(
        #[WithCast(EnumCast::class)]
        public EventTypeEnum $type,
        #[Numeric]
        public mixed $origin,
        #[Min(0)]
        public int $amount,
        #[RequiredIf('type', EventTypeEnum::TRANSFER), Numeric]
        public mixed $destination,
    ) {
    }

    public static function fromRequest(Request $request)
    {
        $data = optional((object)$request->all());

        return new self(
            type: EventTypeEnum::from($data->type),
            origin: (int)$data->origin,
            amount: (int)$data->amount,
            destination: (int)$data->destination,
        );
    }
}
