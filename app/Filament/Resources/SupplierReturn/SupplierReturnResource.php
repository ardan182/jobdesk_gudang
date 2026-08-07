<?php

namespace App\Filament\Resources\SupplierReturn;

use App\Filament\Resources\SupplierReturn\Pages\ListSupplierReturns;
use App\Filament\Resources\SupplierReturn\Schemas\SupplierReturnForm;
use App\Filament\Resources\SupplierReturn\Tables\SupplierReturnsTable;
use App\Models\SupplierReturn;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SupplierReturnResource extends Resource
{
    protected static ?string $model = SupplierReturn::class;

    protected static ?string $slug = 'retur-supplier';

    protected static ?string $navigationLabel = 'Retur In & Out Supplier';

    protected static ?string $modelLabel = 'Retur Supplier';

    protected static ?string $pluralModelLabel = 'Retur Supplier';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-path';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Retur';
    }

    public static function form(Schema $schema): Schema
    {
        return SupplierReturnForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierReturnsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplierReturns::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_supplier_returns') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_supplier_returns') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_supplier_returns') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_supplier_returns') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_supplier_returns') ?? false;
    }
}
