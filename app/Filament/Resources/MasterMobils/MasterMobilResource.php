<?php

namespace App\Filament\Resources\MasterMobils;

use App\Filament\Resources\MasterMobils\Pages\ListMasterMobils;
use App\Filament\Resources\MasterMobils\Schemas\MasterMobilForm;
use App\Filament\Resources\MasterMobils\Tables\MasterMobilsTable;
use App\Models\MasterMobil;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MasterMobilResource extends Resource
{
    protected static ?string $model = MasterMobil::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Master Mobil';

    protected static ?string $modelLabel = 'Master Mobil';

    protected static ?string $pluralModelLabel = 'Master Mobil';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function form(Schema $schema): Schema
    {
        return MasterMobilForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterMobilsTable::configure($table);
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
            'index' => ListMasterMobils::route('/'),
        ];
    }
}
