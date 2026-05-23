<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (! Schema::hasColumn('grades', 'recorded_by')) {
                $table->unsignedBigInteger('recorded_by')->nullable()->after('score');
                $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }
 
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeignIfExists(['recorded_by']);
            $table->dropColumnIfExists('recorded_by');
        });
    }
};
