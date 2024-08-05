<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("workflow_runs", function (Blueprint $table): void {
            $table->bigInteger("workflow_actor_id");
            $table->foreign("workflow_actor_id")->references("id")->on("workflow_actors")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::table("workflow_runs", function (Blueprint $table): void {
            $table->dropColumn("workflow_actor_id");
        });
    }
};
