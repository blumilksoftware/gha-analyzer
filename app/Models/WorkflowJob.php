<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $github_id
 * @property string $name
 * @property int $workflow_run_id
 * @property string $runner_os
 * @property string $runner_type
 * @property int $minutes
 * @property int $multiplier
 * @property float $price_per_unit
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property WorkflowRun $workflowRun
 */
class WorkflowJob extends Model
{
    use HasFactory;

    protected $fillable = [
        "github_id",
        "name",
        "workflow_run_id",
        "runner_os",
        "runner_type",
        "minutes",
        "multiplier",
        "price_per_unit",
    ];

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(WorkflowRun::class);
    }
}
