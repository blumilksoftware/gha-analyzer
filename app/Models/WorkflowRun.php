<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $github_id
 * @property string $name
 * @property int $repository_id
 * @property int $workflow_actor_id
 * @property Carbon $github_created_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Repository $repository
 * @property WorkflowActor $workflowActor
 * @property Collection<WorkflowJob> $workflowJobs
 */
class WorkflowRun extends Model
{
    use HasFactory;

    protected $fillable = [
        "github_id",
        "name",
        "repository_id",
        "github_created_at",
        "workflow_actor_id"
    ];

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function workflowActor(): BelongsTo
    {
        return $this->belongsTo(WorkflowActor::class);
    }

    public function workflowJobs(): HasMany
    {
        return $this->HasMany(WorkflowJob::class);
    }
}
