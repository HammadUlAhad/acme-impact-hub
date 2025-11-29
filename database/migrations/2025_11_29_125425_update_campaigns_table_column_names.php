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
        Schema::table('campaigns', function (Blueprint $table) {
            // Rename target_amount to goal_amount
            $table->renameColumn('target_amount', 'goal_amount');
            // Rename featured_image to image
            $table->renameColumn('featured_image', 'image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Reverse the column renames
            $table->renameColumn('goal_amount', 'target_amount');
            $table->renameColumn('image', 'featured_image');
        });
    }
};
