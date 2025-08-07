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

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->string('tag')->unique();
            $table->text('descripcion')->nullable();
            $table->string('modelo')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('serie')->nullable();
            $table->text('ubicacion')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->date('fecha_puesta_marcha')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('location_id')->constrained();
            $table->foreignId('systems_catalog_id')->constrained();
            $table->foreignId('asset_classification_id')->constrained();
            $table->foreignId('asset_criticality_id')->constrained();
            $table->foreignId('asset_state_id')->constrained()->onDelete('restrict');
            $table->foreignId('asset_parent_id')->nullable()->constrained('assets')->onDelete('set null');
            $table->foreignId('creado_por_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('actualizado_por_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
