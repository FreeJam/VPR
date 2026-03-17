<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function hasRole(string ...$codes): bool
    {
        $roleCodes = $this->roles->pluck('code')->all();

        return collect($codes)->intersect($roleCodes)->isNotEmpty();
    }

    public function primaryRoleCode(): ?string
    {
        $primary = $this->roles->firstWhere('pivot.is_primary', true);

        return $primary?->code ?? $this->roles->first()?->code;
    }

    public function assignRole(string $code, bool $primary = false): void
    {
        $role = Role::query()->where('code', $code)->firstOrFail();

        $this->roles()->syncWithoutDetaching([
            $role->id => ['is_primary' => $primary],
        ]);

        if ($primary) {
            DB::table('user_roles')
                ->where('user_id', $this->id)
                ->where('role_id', '!=', $role->id)
                ->update([
                    'is_primary' => false,
                    'updated_at' => now(),
                ]);
        }

        $this->unsetRelation('roles');
    }
}
