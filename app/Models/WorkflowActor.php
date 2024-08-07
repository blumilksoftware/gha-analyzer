<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $github_id
 * @property string $name
 * @property string $avatar_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property float $totalMinutes
 * @property float $totalPrice
 *
 * @property Collection<WorkflowRun> $workflowRuns
 * @property Collection<WorkflowJob> $workflowJobs
 */
class WorkflowActor extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "github_id",
        "avatar_url",
    ];

    public function workflowRuns(): HasMany
    {
        return $this->hasMany(WorkflowRun::class);
    }

    public function workflowJobs(): HasManyThrough
    {
        return $this->HasManyThrough(WorkflowJob::class, WorkflowRun::class);
    }

    protected function totalMinutes(): Attribute
    {
        return Attribute::get(fn(): float => $this->workflowJobs->sum("minutes"));
    }

    protected function totalPrice(): Attribute
    {
        return Attribute::get(fn(): float => $this->workflowJobs->sum("price"));
    }
}
