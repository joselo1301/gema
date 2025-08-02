<?php

namespace Database\Seeders;

use App\Models\asset;
use App\Models\Location;
use App\Models\systems_catalog;
use App\Models\SystemsCatalog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activos = [
            [1, 10, 'BOYAS DE AMARRE', 3, 1, 1,NULL],
            [1, 10, 'BOYARINES DE IZADO', 3, 1, 1,NULL],
            [1, 10, 'MANGUERAS SUBMARINAS L1-L2', 3, 1, 1,NULL],
            [1, 10, 'TUBERIA DE RECEPCION L1 DE PRODUCTOS CLASE I', 3, 1, 1,NULL],
            [1, 10, 'TUBERIA DE RECEPCION L2 DE PRODUCTOS CLASE II', 3, 1, 1,NULL],
            [1, 10, 'BREAKAWAY', 3, 1, 1,NULL],
            [1, 10, 'LINEA-RECEPCION_PI', 3, 1, 1,NULL],
            [1, 10, 'LINEAS DE RECEPCION', 3, 1, 1,NULL],
            [1, 10, 'MANGUERAS SUBMARINAS', 3, 1, 1,NULL],
            [1, 10, 'LINEA-RECEPCION-ALCOHOL', 3, 1, 1,NULL],
            [1, 10, 'EQUIPOS DE LABORATORIO', 3, 1, 1,NULL],
            [1, 10, 'CONTOMETRO MECANICO', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 15', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 11', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 2', 3, 1, 1,NULL],
            [1, 1, 'TANQUE H1', 3, 1, 1,NULL],
            [1, 1, 'TANQUE H2', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 18', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 19', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 17', 3, 1, 1,NULL],
            [1, 1, 'TANQUE 16', 3, 1, 1,NULL],
            [1, 1, 'TUBERÍAS DE PROCESO', 3, 1, 1,NULL],
            [1, 1, 'SISTEMA DE DRENAJE Y POZA API', 3, 1, 1,NULL],
            [1, 1, 'TUB-PROCESO_IL', 3, 1, 1,NULL],
            [1, 8, 'B-01_IL', 3, 1, 1,NULL],
            [1, 8, 'B-02_IL', 3, 1, 1,NULL],
            [1, 8, 'B-03_IL', 3, 1, 1,NULL],
            [1, 8, 'B-04_IL', 3, 1, 1,NULL],
            [1, 8, 'B-05_IL', 3, 1, 1,NULL],
            [1, 8, 'B-06_IL', 3, 1, 1,NULL],
            [1, 8, 'B-07_IL', 3, 1, 1,NULL],
            [1, 8, 'B-08_IL', 3, 1, 1,NULL],
            [1, 8, 'B-09_IL', 3, 1, 1,NULL],
            [1, 8, 'B-10_IL', 3, 1, 1,NULL],
            [1, 8, 'B-11_IL', 3, 1, 1,NULL],
            [1, 8, 'B-12_IL', 3, 1, 1,NULL],
            [1, 8, 'B-13_IL', 3, 1, 1,NULL],
            [1, 8, 'B-14_IL', 3, 1, 1,NULL],
            [1, 8, 'B-15_IL', 3, 1, 1,NULL],
            [1, 8, 'B-16_IL', 3, 1, 1,NULL],
            [1, 8, 'B-17_IL', 3, 1, 1,NULL],
            [1, 8, 'B-19_IL', 3, 1, 1,NULL],
            [1, 8, 'B-20_IL', 3, 1, 1,NULL],
            [1, 8, 'B-21_IL', 3, 1, 1,NULL],
            [1, 9, 'TURBINAS', 3, 1, 1,NULL],
            [1, 9, 'BIRROTORES', 3, 1, 1,NULL],
            [1, 9, 'CONTOMETROS', 3, 1, 1,NULL],
            [1, 9, 'VALVULA DE CONTROL', 3, 1, 1,NULL],
            [1, 9, 'LINEAS Y ACCESORIOS ADITIVACIÓN', 3, 1, 1,NULL],
            [1, 9, 'DANLOAD', 3, 1, 1,NULL],
            [1, 9, 'SISTEMA DE SOBRELLENADO', 3, 1, 1,NULL],
            [1, 9, 'SISTEMA DE PUESTA A TIERRA', 3, 1, 1,NULL],
            [1, 9, 'TABLERO PLC', 3, 1, 1,NULL],
            [1, 9, 'RTD', 3, 1, 1,NULL],
            [1, 9, 'FILTROS', 3, 1, 1,NULL],
            [1, 9, 'ELIMINADOR DE AIRE', 3, 1, 1,NULL],
            [1, 9, 'BRAZOS DE CARGA Y DESCARGA', 3, 1, 1,NULL],
            [1, 9, 'MANGUERAS ACOPLADORES RECUPERACION DE VAPORES.', 3, 1, 1,NULL],
            [1, 9, 'ARRESTAFLAMA', 3, 1, 1,NULL],
            [1, 9, 'TANQUES', 3, 1, 1,NULL],
            [1, 9, 'TABLEROS Y BOMBAS DE ADITIVOS.', 3, 1, 1,NULL],
            [1, 9, 'MANTENIMIENTO DE TABLERO TG Y TA-2.', 3, 1, 1,NULL],
            [1, 9, 'DANLOAD MEGADO', 3, 1, 1,NULL],
            [1, 9, 'PUENTE DE PRECINTADO - SISTEMA ANTICAIDA', 3, 1, 1,NULL],
            [1, 9, 'PUENTE DE PRECINTADO - ESTRUCTURA', 3, 1, 1,NULL],
            [1, 2, 'POSTES DE ALUMBRADO', 2, 3, 1,NULL],
            [1, 2, 'PUESTAS A TIERRA', 2, 3, 1,NULL],
            [1, 2, 'PARARRAYOS', 2, 3, 1,NULL],
            [1, 2, 'UPS', 2, 1, 1,NULL],
            [1, 2, 'TRANQUERA DE VIGILANCIA', 2, 3, 1,NULL],
            [1, 2, 'BANCO DE CONDENSADORES', 2, 3, 1,NULL],
            [1, 2, 'SECCIONADORES DISYUNTORES TRANSFORMADORES', 2, 2, 1,NULL],
            [1, 2, 'TABLEROS DE TRANSFERENCIA ILUMINACION DISTRIBUCIÓN', 2, 2, 1,NULL],
            [1, 2, 'LABORATORIO', 2, 2, 1,NULL],
            [1, 2, 'SISTEMA DE PROTECCIÓN CATÓDICA DE LINEA SUBMARINA', 2, 2, 1,NULL],
            [1, 2, 'EQUIPOS AIRE ACONDICIONADO', 1, 3, 1,NULL],
            [1, 2, 'PANEL DE ENTRADA AL TERMINAL', 1, 3, 1,NULL],
            [1, 2, 'JARDIN AREAS VERDES', 1, 3, 1,NULL],
            [1, 2, 'ALARMA SONORA', 1, 3, 1,NULL],
            [1, 2, 'OFICINAS Y ALMACENES', 1, 3, 1,NULL],
            [1, 2, 'PISTA DEL TERMINAL VIAS Y VEREDAS', 1, 3, 1,NULL],
            [1, 2, 'MURO PERIMÉTRICO', 1, 3, 1,NULL],
            [1, 2, 'TANQUE DE AGUA PARA OFICINAS', 1, 3, 1,NULL],
            [1, 2, 'RED DE AGUA Y DESAGUE', 1, 3, 1,NULL],
            [1, 5, 'GRUPO ELECTROGENO', 2, 2, 1,NULL],
            [1, 7, 'EXTINTORES', 2, 3, 1,NULL],
            [1, 7, 'TANQUE DE AGUA CONTRA INCENDIO', 2, 3, 1,NULL],
            [1, 7, 'MONITORES', 2, 3, 1,NULL],
            [1, 7, 'HIDRANTES', 2, 3, 1,NULL],
            [1, 7, 'MOTOBOMBA 1', 2, 2, 1,NULL],
            [1, 7, 'GABINETES', 2, 3, 1,NULL],
            [1, 7, 'TANQUE DE ESPUMA', 2, 3, 1,NULL],
            [1, 7, 'BOMBA JOCKEY', 2, 2, 1,NULL],
            [1, 7, 'LINEAS RED CONTRA INCENDIO', 2, 3, 1,NULL],
            [1, 7, 'TRAILER CONTRA DERRAME.', 2, 3, 1,NULL],
            [1, 7, 'BARRERAS', 2, 3, 1,NULL],
            [1, 7, 'MOTOBOMBAS CONTRA DERRAME', 2, 3, 1,NULL],
            [1, 7, 'SKIMMER', 2, 3, 1,NULL],
            [1, 4, 'EQUIPOS DE LABORATORIO Y DE INSPECCIÓN', 3, 1, 1,NULL],
            [1, 10, 'BOYAS DE AMARRE A1', 3, 1, 1, 1],
            [1, 10, 'BOYAS DE AMARRE A2', 3, 1, 1, 1],
            [1, 10, 'BOYAS DE AMARRE A3', 3, 1, 1, 1],

        ];

        foreach ($activos as $i => [$location_id, $systems_catalog_id, $nombre, $classification_id, $criticality_id, $state_id, $parent_id]) {

            $location_codigo = Location::find($location_id)?->codigo ?? 'LOC';
            $system_codigo = SystemsCatalog::find($systems_catalog_id)?->codigo ?? 'SYS';
            $slug = Str::slug(substr($nombre, 0, 30), '_');
            $codigo = "{$location_codigo}-{$system_codigo}-{$slug}";


            Asset::insert([
                'location_id' => $location_id,
                'systems_catalog_id' => $systems_catalog_id,
                'asset_classification_id' => $classification_id,
                'asset_criticality_id' => $criticality_id,
                'asset_state_id' => $state_id,
                'asset_parent_id' => $parent_id,
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tag' => 'TAG-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'creado_por_id' => 1, // asegúrate de tener este usuario
                'actualizado_por_id' => 1,
            ]);
        }
    }
}
