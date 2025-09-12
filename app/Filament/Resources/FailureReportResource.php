<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FailureReportResource\Forms\FailureReportForm;
use App\Filament\Resources\FailureReportResource\Tables\FailureReportTable;
use App\Filament\Resources\FailureReportResource\Infolists\FailureReportInfolist;
use App\Filament\Resources\FailureReportResource\Pages;
use App\Filament\Resources\FailureReportResource\RelationManagers;
use App\Models\Asset;
use App\Models\FailureReport;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Auth;

class FailureReportResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FailureReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Reportes de falla';
    protected static ?string $modelLabel = 'Reporte de falla';
    protected static ?string $pluralModelLabel = 'Reportes de falla';


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'reportar',
            'aprobar',
            'rechazar',
            'cambiar_etapa',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }
    // llama al formulario
    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema(FailureReportForm::getForm());
    }

    public static function table(Table $table): Table
    {
        return FailureReportTable::getTable($table);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return FailureReportInfolist::getInfolist($infolist);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Bitácora', [
                ActivitylogRelationManager::class,
            ])
            ->icon('heroicon-m-eye'), 
        ];  
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailureReports::route('/'),
            'create' => Pages\CreateFailureReport::route('/create'),
            'edit' => Pages\EditFailureReport::route('/{record}/edit'),
            'view' => Pages\ViewFailureReport::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        $query = parent::getEloquentQuery()
        ->whereIn('location_id', $user->locations->pluck('id'));

        if (! $user->hasAnyRole(['CreadorRF', 'ReportanteRF'])) {
            $query->excludeFollowupId(1);
        }

        return $query;
    }

}
