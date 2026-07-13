<?php

namespace App\Filament\Resources\WarehouseEmployees;

use App\Filament\Resources\WarehouseEmployees\Pages\ListWarehouseEmployees;
use App\Filament\Resources\WarehouseEmployees\Schemas\WarehouseEmployeeForm;
use App\Filament\Resources\WarehouseEmployees\Tables\WarehouseEmployeesTable;
use App\Models\WarehouseEmployee;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WarehouseEmployeeResource extends Resource
{
    protected static ?string $model = WarehouseEmployee::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Master Employee Gudang';

    protected static ?string $modelLabel = 'Master Employee Gudang';

    protected static ?string $pluralModelLabel = 'Master Employee Gudang';

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    public static function form(Schema $schema): Schema
    {
        return WarehouseEmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehouseEmployeesTable::configure($table);
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
            'index' => ListWarehouseEmployees::route('/'),
        ];
    }
}
