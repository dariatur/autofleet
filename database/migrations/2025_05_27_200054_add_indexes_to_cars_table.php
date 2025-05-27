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
        Schema::table('cars', function (Blueprint $table) {
            $table->index('make');
            $table->index('year');
            $table->index('price');
            $table->index(['make', 'year']);
            $table->index(['make', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex(['make']);
            $table->dropIndex(['year']);
            $table->dropIndex(['price']);
            $table->dropIndex(['make', 'year']);
            $table->dropIndex(['make', 'price']);
        });
    }
};
