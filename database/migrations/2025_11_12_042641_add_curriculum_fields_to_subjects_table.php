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
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('description')->nullable()->after('title');
            $table->unsignedTinyInteger('lec_hours')->default(0)->after('term');
            $table->unsignedTinyInteger('lab_hours')->default(0)->after('lec_hours');
            $table->unsignedTinyInteger('credit_units')->default(0)->after('lab_hours');
            $table->unsignedTinyInteger('tuition_hours')->default(0)->after('credit_units');
            $table->string('prereq')->nullable()->after('tuition_hours');
            $table->string('coreq')->nullable()->after('prereq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'lec_hours',
                'lab_hours',
                'credit_units',
                'tuition_hours',
                'prereq',
                'coreq'
            ]);
        });
    }
};
