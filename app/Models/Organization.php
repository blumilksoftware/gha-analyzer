<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $github_id
 * @property string $name
 * @property string $avatar_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection<User> $users
 * @property Collection<Repository> $repositories
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "github_id",
        "avatar_url",
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_organization");
    }

    public function repositories(): HasMany
    {
        return $this->HasMany(Repository::class);
    }
}
