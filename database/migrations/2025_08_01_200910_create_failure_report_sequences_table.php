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
        Schema::disableForeignKeyConstraints();

        Schema::create('failure_report_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_locacion');     // ej. "IL"
            $table->unsignedSmallInteger('year');  // ej. 2025
            $table->unsignedInteger('current')->default(0); // último correlativo usado
            $table->timestamps();

            $table->unique(['codigo_locacion', 'year']); // clave única
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failure_report_sequences');
    }
};
