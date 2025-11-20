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
        Schema::table('feed_items', function (Blueprint $table) {
            // Allow duplicate links across different feeds while keeping an index for querying.
            $table->dropUnique('feed_items_link_unique');
            $table->index('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_items', function (Blueprint $table) {
            $table->dropIndex('feed_items_link_index');
            $table->unique('link');
        });
    }
};

