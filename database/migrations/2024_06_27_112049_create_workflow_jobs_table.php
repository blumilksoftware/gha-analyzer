<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("workflow_jobs", function (Blueprint $table): void {
            $table->id();
            $table->bigInteger("github_id");
            $table->string("name");
            $table->bigInteger("workflow_run_id");
            $table->string("runner_os");
            $table->string("runner_type");
            $table->integer("minutes");
            $table->integer("multiplier");
            $table->float("price_per_unit");
            $table->foreign("workflow_run_id")->references("id")->on("workflow_runs")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("workflow_jobs");
    }
};
