<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property float $price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property string $fullName
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

    protected function price(): Attribute
    {
        return Attribute::get(fn(): float => $this->minutes * $this->price_per_unit);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn(): string => $this->workflowRun->name . " - " . $this->name);
    }
}
