<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table("organizations", function (Blueprint $table): void {
            $table->timestamp("fetch_at")->nullable();
        });
    }

    public function down(): void
    {
        Schema::table("organizations", function (Blueprint $table): void {
            $table->dropColumn("fetch_at");
        });
    }
};
