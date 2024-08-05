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
 * @property int $organization_id
 * @property boolean $is_private
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Organization $organization
 * @property Collection<WorkflowRun> $workflowRuns
 */
class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        "github_id",
        "name",
        "organization_id",
        "is_private",
    ];

    public function workflowRuns(): HasMany
    {
        return $this->HasMany(WorkflowRun::class);
    }

    public function organization(): BelongsTo
    {
        return $this->BelongsTo(Organization::class);
    }
}
