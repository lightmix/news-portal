<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Middleware\ProtectGuestUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Test User3',
            'email' => 'test3@example.com',
            'password' => '12345678',
        ]);

        User::factory()->create([
            'name' => 'Guest',
            'email' => ProtectGuestUser::GUEST_USER_EMAIL,
            'password' => '12345678',
        ]);
    }
}
