<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create("user_organization", function (Blueprint $table): void {
            $table->id();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("organization_id")->references("id")->on("organizations")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_organization");
    }
};
