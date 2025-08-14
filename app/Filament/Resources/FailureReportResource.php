<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FailureReportResource\Pages;
use App\Filament\Resources\FailureReportResource\RelationManagers;
use App\Models\Asset;
use App\Models\FailureReport;
use App\Models\FailureReportSequence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class FailureReportResource extends Resource
{
    protected static ?string $model = FailureReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Reportes de falla';
    protected static ?string $modelLabel = 'Reporte de falla';
    protected static ?string $pluralModelLabel = 'Reportes de falla';


    public static function form(Form $form): Form
    {
        return $form

            ->columns(1)
            ->schema([
            
            Wizard::make([
            Step::make('Seleccionar activo')
                ->schema([

                    Forms\Components\Select::make('asset_id')
                        ->relationship('asset', 'nombre')
                        ->getOptionLabelFromRecordUsing(function ($record) {
                            $locationName = $record->location?->nombre ?? '';
                            return "{$record->nombre} ({$record->tag}) - {$locationName}";
                        })
                        ->searchable(['nombre', 'tag'])
                        ->noSearchResultsMessage('Activo no encontrado...')
                        ->required(),
                        
                        
                ]),
            Step::make('Delivery')
                ->schema([
                    Forms\Components\TextInput::make('numero_reporte')
                            ->required()
                            ->maxLength(255),
                                // ->default(function ($get) {
                                //     // Obtener el asset_id para el código de locación
                                //     $assetId = $get('asset_id');
                                //     $codigoLocacion = '';

                                //     if ($assetId) {
                                //         $asset = Asset::with('location')->find($assetId);
                                //         $codigoLocacion = $asset?->location?->codigo ?? ''; 
                                //     }       

                                //     // // Obtener el año actual
                                //     // $year = date('Y');

                                //     // // Buscar el correlativo actual en FailureReportSequence
                                //     // $correlativo = FailureReportSequence::where('codigo_locacion', $codigoLocacion)
                                //     //     ->where('year', $year)
                                //     //     ->first();

                                //     // if (!$correlativo) {
                                //     //     // Si no existe, crear uno nuevo
                                //     //     $correlativo = FailureReportSequence::create([
                                //     //         'codigo_locacion' => $codigoLocacion,
                                //     //         'year' => $year,
                                //     //         'correlativo' => 1,
                                //     //     ]);
                                //     // }

                                //     // Formatear el número de reporte
                                //     return 'RF-' . $codigoLocacion . '-' . str_pad($correlativo->correlativo, 4, '0', STR_PAD_LEFT) . '-' . $year;
                                // }),
                        Forms\Components\DateTimePicker::make('fecha_ocurrencia')
                            ->required(),
                        Forms\Components\Textarea::make('datos_generales')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('descripcion_corta')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('personal_detector')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('descripcion_detallada')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('causas_probables')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('acciones_realizadas')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('afecta_operaciones')
                            ->required(),
                        Forms\Components\Toggle::make('afecta_medio_ambiente')
                            ->required(),
                        Forms\Components\Textarea::make('apoyo_adicional')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('observaciones')
                            ->columnSpanFull(),
                ]),
            Step::make('Billing')
                ->schema([
                    
                        Forms\Components\Select::make('report_status_id')
                            ->relationship('reportStatus', 'id')
                            ->required(),
                        Forms\Components\Select::make('report_followup_id')
                            ->relationship('reportFollowup', 'id')
                            ->required(),
                        Forms\Components\Select::make('creado_por_id')
                            ->relationship('creadoPor', 'name')
                            ->required(),
                        Forms\Components\Select::make('reportado_por_id')
                            ->relationship('reportadoPor', 'name'),
                        Forms\Components\DateTimePicker::make('reportado_en'),
                        Forms\Components\Select::make('aprobado_por_id')
                            ->relationship('aprobadoPor', 'name'),
                        Forms\Components\DateTimePicker::make('aprobado_en'),
                        Forms\Components\Select::make('ejecutado_por_id')
                            ->relationship('ejecutadoPor', 'name'),
                        Forms\Components\Select::make('actualizado_por_id')
                            ->relationship('actualizadoPor', 'name'),
                ]),
            ]),
                
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_reporte')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_ocurrencia')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion_corta')
                    ->searchable(),
                Tables\Columns\IconColumn::make('afecta_operaciones')
                    ->boolean(),
                Tables\Columns\IconColumn::make('afecta_medio_ambiente')
                    ->boolean(),
                Tables\Columns\TextColumn::make('asset.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assetParent.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assetState.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportStatus.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportFollowup.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportado_en')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aprobadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aprobado_en')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ejecutadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actualizadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailureReports::route('/'),
            'create' => Pages\CreateFailureReport::route('/create'),
            'edit' => Pages\EditFailureReport::route('/{record}/edit'),
        ];
    }
}
