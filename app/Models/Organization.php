<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $github_id
 * @property string $name
 * @property string $avatar_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $fetch_at
 *
 * @property float $totalMinutes
 * @property float $totalPrice
 *
 * @property Collection<User> $users
 * @property Collection<Repository> $repositories
 * @property Collection<WorkflowRun> $workflowRuns
 * */
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

    public function workflowRuns(): HasManyThrough
    {
        return $this->HasManyThrough(WorkflowRun::class, Repository::class);
    }

    protected function casts(): array
    {
        return [
            "fetch_at" => "datetime",
        ];
    }

    protected function totalMinutes(): Attribute
    {
        return Attribute::get(fn(): float => $this->repositories->sum("totalMinutes"));
    }

    protected function jobCount(): Attribute
    {
        return Attribute::get(fn(): int => $this->workflowRuns()
            ->withCount("workflowJobs")
            ->get()
            ->sum("workflow_jobs_count"));
    }

    protected function actorCount(): Attribute
    {
        return Attribute::get(fn(): int => $this->workflowRuns()
            ->pluck("workflow_actor_id")
            ->unique()
            ->count());
    }

    protected function totalPrice(): Attribute
    {
        return Attribute::get(fn(): float => $this->repositories->sum("totalPrice"));
    }
}
