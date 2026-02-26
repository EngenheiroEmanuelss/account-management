<?php

namespace Feature\Api\Http\Controllers;

use App\Enum\EventTypeEnum;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $migrate = true;
    protected $seed = true;
    public int $originAccountId = 100;
    public int $destinationAccountId = 200;
    public int $baseAmount = 50;
    public string $api = '/api/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeader('Accept', 'application/json');
    }

    private function createAccount(int $id, int $amount)
    {
        return Account::factory()
            ->create([
                'id'                => $id,
                'available_balance' => $amount
            ]);
    }

    #[Test]
    public function itResetAccounts(): void
    {
        $this->post($this->api . 'reset')
            ->assertStatus(200);
    }

    #[Test]
    public function itResetAccountsWhenExists(): void
    {
        $account = $this->createAccount($this->originAccountId, $this->baseAmount);

        $this->assertDatabaseHas(Account::class, [
            'id' => $account->id,
        ]);

        $this->post($this->api . 'reset')
            ->assertStatus(200);

        $this->assertDatabaseEmpty(Account::class);
    }

    #[Test]
    public function itReturnBalance(): void
    {
        $account = $this->createAccount($this->originAccountId, $this->baseAmount);

        $this->get($this->api . 'balance?account_id=' . $account->id)
            ->assertContent((string)$this->baseAmount)
            ->assertStatus(200);
    }

    #[Test]
    public function itThrowsWhenNotFoundAccount(): void
    {
        $id = 1234;

        $this->get($this->api . 'balance?account_id=' . $id)
            ->assertStatus(404);
    }

    #[Test]
    public function itThrowsOnWithdrawWhenInsufficientBalance(): void
    {
        $account = $this->createAccount($this->originAccountId, $this->baseAmount);

        $withdrawPayload = [
            "type"   => EventTypeEnum::WITHDRAW->value,
            "origin" => $account->id,
            "amount" => 500
        ];

        $this->post($this->api . 'event', $withdrawPayload)
            ->assertStatus(422);
    }

    #[Test]
    public function itThrowsOnTransferWhenInsufficientBalance(): void
    {
        $originAccount      = $this->createAccount($this->originAccountId, $this->baseAmount);
        $destinationAccount = $this->createAccount($this->destinationAccountId, $this->baseAmount);

        $transferPayload = [
            "type"        => EventTypeEnum::TRANSFER->value,
            "origin"      => $originAccount->id,
            "amount"      => 1500,
            "destination" => $destinationAccount->id
        ];

        $this->post($this->api . 'event', $transferPayload)
            ->assertStatus(422);
    }

    #[Test]
    public function itThrowsWhenUnknownTransactionType(): void
    {
        $transferPayload = [
            "type" => 'unknown',
        ];

        $this->post($this->api . 'event', $transferPayload)
            ->assertStatus(422);
    }

    #[Test]
    public function itCreateAccount(): void
    {
        $depositPayload = [
            "type"        => EventTypeEnum::DEPOSIT->value,
            "destination" => $this->destinationAccountId,
            "amount"      => $this->baseAmount
        ];

        $response = $this->post($this->api . 'event', $depositPayload)
            ->assertJsonFragment([
                "destination" => [
                    "id"      => (string)$this->destinationAccountId,
                    "balance" => $this->baseAmount
                ]
            ])->assertStatus(201);

        $responseObject = json_decode($response->getContent());

        $this->assertDatabaseHas(Account::class, [
            'id'                => $responseObject->destination->id,
            'available_balance' => $this->baseAmount,
        ]);
    }

    #[Test]
    public function itAddBalanceIntoAccount(): void
    {
        $amount = 30;

        $account = $this->createAccount($this->originAccountId, $this->baseAmount);

        $depositPayload = [
            "type"        => EventTypeEnum::DEPOSIT->value,
            "destination" => $account->id,
            "amount"      => $amount
        ];

        $this->post($this->api . 'event', $depositPayload)
            ->assertJsonFragment([
                "destination" => [
                    "id"      => (string)$account->id,
                    "balance" => $this->baseAmount + $amount
                ]
            ])->assertStatus(201);

        $this->assertDatabaseHas(Account::class, [
            'id'                => $account->id,
            'available_balance' => $this->baseAmount + $amount
        ]);
    }

    #[Test]
    public function itTransferFromExistingAccount(): void
    {
        $amountTransfer = 20;

        $originAccount      = $this->createAccount($this->originAccountId, $this->baseAmount);
        $destinationAccount = $this->createAccount($this->destinationAccountId, $this->baseAmount);

        $transferPayload = [
            "type"        => EventTypeEnum::TRANSFER->value,
            "origin"      => $originAccount->id,
            "amount"      => $amountTransfer,
            "destination" => $destinationAccount->id
        ];

        $this->post($this->api . 'event', $transferPayload)
            ->assertStatus(201);

        $this->assertDatabaseHas(Account::class, [
            'id'                => $originAccount->id,
            'available_balance' => $this->baseAmount - $amountTransfer,
        ]);

        $this->assertDatabaseHas(Account::class, [
            'id'                => $destinationAccount->id,
            'available_balance' => $this->baseAmount + $amountTransfer,
        ]);
    }

    #[Test]
    public function itThrowsWhenTransferFromExistingAccountForInexistentAccount(): void
    {
        $amountTransfer = 20;

        $originAccount      = $this->createAccount($this->originAccountId, $this->baseAmount);
        $destinationAccount = 1234;

        $transferPayload = [
            "type"        => EventTypeEnum::TRANSFER->value,
            "origin"      => $originAccount->id,
            "amount"      => $amountTransfer,
            "destination" => $destinationAccount
        ];

        $this->post($this->api . 'event', $transferPayload)
            ->assertStatus(404);

        $this->assertDatabaseHas(Account::class, [
            'id'                => $originAccount->id,
            'available_balance' => $this->baseAmount,
        ]);
    }

    #[Test]
    public function itWithdrawAccount(): void
    {
        $amount = 20;

        $account = $this->createAccount($this->originAccountId, $this->baseAmount);

        $withdrawPayload = [
            "type"   => EventTypeEnum::WITHDRAW->value,
            "origin" => $account->id,
            "amount" => $amount
        ];

        $this->post($this->api . 'event', $withdrawPayload)
            ->assertStatus(201);

        $this->assertDatabaseHas(Account::class, [
            'id'                => $account->id,
            'available_balance' => $this->baseAmount - $amount,
        ]);
    }
}
