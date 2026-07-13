<?php

namespace App\Filament\Resources\MasterTokos;

use App\Filament\Resources\MasterTokos\Pages\ListMasterTokos;
use App\Filament\Resources\MasterTokos\Schemas\MasterTokoForm;
use App\Filament\Resources\MasterTokos\Tables\MasterTokosTable;
use App\Models\MasterToko;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MasterTokoResource extends Resource
{
    protected static ?string $model = MasterToko::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Master Toko';

    protected static ?string $modelLabel = 'Master Toko';

    protected static ?string $pluralModelLabel = 'Master Toko';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function form(Schema $schema): Schema
    {
        return MasterTokoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterTokosTable::configure($table);
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
            'index' => ListMasterTokos::route('/'),
        ];
    }
}
