<?php declare(strict_types=1);

namespace Tests;

use App\Http\Middleware\ProtectGuestUser;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedAndGetGuestUser(): User
    {
        $this->seed();

        return User::query()
            ->where('email', ProtectGuestUser::GUEST_USER_EMAIL)
            ->first();
    }
}
