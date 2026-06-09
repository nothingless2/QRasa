<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes to the menus table.
     * NOTE: nama and kategori indexes already exist (checked via DESCRIBE menus).
     * This migration adds a composite index for combined filter queries.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Composite index: stok + kategori for "available items in category" queries
            // Prevents full table scan when filtering by category on high-stock items
            if (!$this->indexExists('menus', 'menus_stok_kategori_index')) {
                $table->index(['stok', 'kategori'], 'menus_stok_kategori_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndexIfExists('menus_stok_kategori_index');
        });
    }

    /**
     * Check whether an index already exists to avoid duplicate index errors.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();

        $exists = $connection->selectOne(
            "SELECT COUNT(*) as count FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$dbName, $table, $indexName]
        );

        return $exists && $exists->count > 0;
    }
};
