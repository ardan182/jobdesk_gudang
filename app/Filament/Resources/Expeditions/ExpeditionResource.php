<?php

namespace App\Filament\Resources\Expeditions;

use App\Filament\Resources\Expeditions\Pages\ListExpeditions;
use App\Filament\Resources\Expeditions\Schemas\ExpeditionForm;
use App\Filament\Resources\Expeditions\Tables\ExpeditionsTable;
use App\Models\Expedition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExpeditionResource extends Resource
{
    protected static ?string $model = Expedition::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Master Ekspedisi';

    protected static ?string $modelLabel = 'Master Ekspedisi';

    protected static ?string $pluralModelLabel = 'Master Ekspedisi';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function form(Schema $schema): Schema
    {
        return ExpeditionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpeditionsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpeditions::route('/'),
        ];
    }
}
