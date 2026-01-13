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
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->foreignId('faculty_document_id')->nullable()->after('portfolio_id')->constrained('faculty_documents')->nullOnDelete();
            $table->index('faculty_document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropForeign(['faculty_document_id']);
            $table->dropIndex(['faculty_document_id']);
            $table->dropColumn('faculty_document_id');
        });
    }
};
