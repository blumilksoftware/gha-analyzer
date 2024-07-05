<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("workflow_runs", function (Blueprint $table): void {
            $table->id();
            $table->bigInteger("github_id");
            $table->string("name");
            $table->bigInteger("repository_id");
            $table->timestamp("github_created_at");
            $table->timestamps();
            $table->foreign("repository_id")->references("id")->on("repositories")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("workflow_runs");
    }
};
