<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("repositories", function (Blueprint $table): void {
            $table->id();
            $table->bigInteger("github_id");
            $table->string("name");
            $table->bigInteger("organization_id");
            $table->boolean("is_private");
            $table->timestamps();
            $table->foreign("organization_id")->references("id")->on("organizations")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("repositories");
    }
};
