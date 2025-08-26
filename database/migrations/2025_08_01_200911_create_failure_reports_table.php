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

        Schema::create('failure_reports', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reporte')->unique();
            $table->dateTime('fecha_ocurrencia');
            $table->text('datos_generales');
            $table->string('descripcion_corta');
            $table->text('descripcion_detallada');
            $table->text('causas_probables');
            $table->text('acciones_realizadas');
            $table->boolean('afecta_operaciones')->default(false);
            $table->boolean('afecta_medio_ambiente')->default(false);
            $table->text('apoyo_adicional')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('location_id')->constrained()->onDelete('restrict');
            $table->foreignId('report_status_id')->constrained()->onDelete('restrict');
            $table->foreignId('report_followup_id')->constrained()->onDelete('restrict');
            $table->foreignId('creado_por_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('reportado_por_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->dateTime('reportado_en')->nullable();
            $table->foreignId('aprobado_por_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->dateTime('aprobado_en')->nullable();
            $table->foreignId('ejecutado_por_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->json('approved_snapshot')->nullable();  // payload con todo lo necesario
            $table->string('approved_hash', 64)->nullable()->index(); // opcional para integridad
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failure_reports');
    }
};
