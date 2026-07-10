<?php

namespace App\Filament\Resources\MasterSopirs;

use App\Filament\Resources\MasterSopirs\Pages\ListMasterSopirs;
use App\Filament\Resources\MasterSopirs\Schemas\MasterSopirForm;
use App\Filament\Resources\MasterSopirs\Tables\MasterSopirsTable;
use App\Models\MasterSopir;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MasterSopirResource extends Resource
{
    protected static ?string $model = MasterSopir::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Master Sopir';

    protected static ?string $modelLabel = 'Master Sopir';

    protected static ?string $pluralModelLabel = 'Master Sopir';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function form(Schema $schema): Schema
    {
        return MasterSopirForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MasterSopirsTable::configure($table);
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
            'index' => ListMasterSopirs::route('/'),
        ];
    }
}
