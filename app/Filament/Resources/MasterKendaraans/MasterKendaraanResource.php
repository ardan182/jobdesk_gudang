<?php

namespace App\Filament\Resources\MasterKendaraans;

use App\Filament\Resources\MasterKendaraans\Pages\ListMasterKendaraans;
use App\Filament\Resources\MasterKendaraans\Schemas\MasterKendaraanForm;
use App\Filament\Resources\MasterKendaraans\Tables\MasterKendaraansTable;
use App\Models\MasterKendaraan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MasterKendaraanResource extends Resource
{
    protected static ?string $model = MasterKendaraan::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Master Kendaraan';

    protected static ?string $modelLabel = 'Master Kendaraan';

    protected static ?string $pluralModelLabel = 'Master Kendaraan';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function form(Schema $schema): Schema
    {
        return MasterKendaraanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterKendaraansTable::configure($table);
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
            'index' => ListMasterKendaraans::route('/'),
        ];
    }
}
