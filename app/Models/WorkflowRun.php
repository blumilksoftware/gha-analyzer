<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowRun extends Model
{
    use HasFactory;

    protected $fillable = [
        "github_id",
        "name",
        "repository_id",
        "created_at",
    ];

    public function workflowJobs(): HasMany
    {
        return $this->HasMany(WorkflowJob::class);
    }

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
}
