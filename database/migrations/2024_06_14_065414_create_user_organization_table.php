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
            $table->bigInteger("user_id");
            $table->bigInteger("organization_id");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("user_organization");
    }
};
