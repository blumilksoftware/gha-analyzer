<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
