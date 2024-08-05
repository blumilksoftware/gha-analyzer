<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workflow_runs', function (Blueprint $table) {
            $table->bigInteger("workflow_actor_id");
            $table->foreign("workflow_actor_id")->references("id")->on("workflow_actors")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_runs', function (Blueprint $table) {
            $table->dropColumn("workflow_actor_id");
        });
    }
};
