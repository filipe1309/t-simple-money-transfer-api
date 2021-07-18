<?php

namespace App\Observers;

use App\Models\User;
use Ramsey\Uuid\Uuid;

class UserObserver
{
    public function created(User $user): void
    {
        $user->wallets()->create([
            'id' => Uuid::uuid4()->toString(),
            'balance' => 0
        ]);
    }
}
