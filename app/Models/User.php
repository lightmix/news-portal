<?php declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property ?CarbonImmutable $deleted_at
 * @property ?CarbonImmutable $confirmed_at
 * @property string $name
 * @property string $email
 * @property ?CarbonImmutable $email_verified_at
 * @property string $password
 * @property ?string $remember_token
 * @property ?int $telegram_id
 */
class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function routeNotificationForTelegram(): ?int
    {
        return $this->telegram_id;
    }
}
