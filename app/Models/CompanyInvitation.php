<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $company_id
 * @property int|null $invited_by
 * @property string $email
 * @property string $token_hash
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['email', 'invited_by', 'token_hash', 'expires_at'])]
#[Hidden(['token_hash'])]

class CompanyInvitation extends Model
{
    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'company_id' => 'integer',
            'invited_by' => 'integer',
            'expires_at' => 'datetime',
        ];
    }
}
