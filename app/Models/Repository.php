<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
