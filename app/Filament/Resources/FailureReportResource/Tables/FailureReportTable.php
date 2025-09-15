<?php

namespace App\Filament\Resources\FailureReportResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

class FailureReportTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('numero_reporte')
                    ->label('N° Reporte')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('asset.nombre')
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn ($record): string => $record->descripcion_corta ?? '')
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_ocurrencia')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion_corta')
                    ->toggleable(isToggledHiddenByDefault: true)    
                    ->label('Descripción Corta')
                    //                     ->wrap()
                    // ->lineClamp(2)
                    
                    ->searchable(),

                Tables\Columns\TextColumn::make('location.nombre')
                    ->label('Ubicación')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('reportStatus.nombre')
                    ->label('Estado')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->reportStatus?->color)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('reportFollowup.nombre')
                    ->label('Etapa')                    
                    ->sortable()
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $c = $record->reportFollowup->color ?? 'gray';

                        // ¿Color hex (#...) o clave del tema (primary, success, etc.)?
                        $isHex = is_string($c) && str_starts_with($c, '#');

                        if ($isHex) {
                            $text   = $c;
                            $border = $c;
                            $bg     = 'transparent'; // Mantén el fondo limpio para mantener legibilidad corporativa
                        } else {
                            // Colores del tema de Filament (se adaptan a light/dark)
                            $text   = "var(--{$c}-600)";
                            $border = "var(--{$c}-400)";
                            $bg     = "var(--{$c}-50)";
                        }

                                return <<<HTML
                        <span style="
                            display:inline-flex;align-items:center;gap:.5rem;
                            padding:.25rem .625rem;border:1px solid {$border};
                            border-radius:.5rem;background:{$bg};
                            color:{$text};font-weight:600;font-size:.75rem;line-height:1;
                        ">
                            <span style="
                                width:.5rem;height:.5rem;border-radius:9999px;
                                background:{$text};flex-shrink:0;
                            "></span>
                            <span style="white-space:nowrap;">{$state}</span>
                        </span>
                        HTML;
                    }),

                         
                    
                Tables\Columns\TextColumn::make('creadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('reportadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('aprobadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ejecutadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('actualizadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('reportStatus_id')
                    ->label('Estado de reporte')
                    ->relationship('reportStatus', 'nombre' , fn ($query) => $query->orderBy('orden')),
                // Filtro por Sistema
                Tables\Filters\SelectFilter::make('reportFollowup_id')
                    ->label('Etapa de Reporte')
                    ->relationship('reportFollowup', 'nombre', fn ($query) => $query->orderBy('orden')),
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'nombre', function ($query) {
                        $user = Filament::auth()->user();
                        return $query->whereIn('id', $user->locations->pluck('id'))->orderBy('nombre');
                    })
                    ->visible(fn () => Filament::auth()->user()->locations->count() > 1),
                
            ])
            ->actions([
                ActivityLogTimelineTableAction::make('Historial')
                    ->label('Historial')
                    ->tooltip('Historial')
                    ->withRelations(['profile', 'address'])
                    ->icon('heroicon-s-eye')
                    ->timelineIconColors([
                        'created' => 'success',
                        'updated' => 'info',
                        'restored' => 'warning',                       
                    ])
                    ->limit(10),

                CommentsAction::make()
                    ->label('')
                    ->tooltip('Comentarios')
                ,
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make()
                        
                ]),
            ]);
    }
}