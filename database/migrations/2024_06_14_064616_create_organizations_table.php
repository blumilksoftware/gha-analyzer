<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("organizations", function (Blueprint $table): void {
            $table->id();
            $table->bigInteger("github_id")->unique();
            $table->string("name");
            $table->string("avatar_url");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("organizations");
    }
};
