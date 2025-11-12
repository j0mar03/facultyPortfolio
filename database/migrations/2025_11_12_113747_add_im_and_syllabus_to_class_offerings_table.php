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
        Schema::table('class_offerings', function (Blueprint $table) {
            $table->string('instructional_material')->nullable()->after('assignment_document');
            $table->string('syllabus')->nullable()->after('instructional_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_offerings', function (Blueprint $table) {
            $table->dropColumn(['instructional_material', 'syllabus']);
        });
    }
};
