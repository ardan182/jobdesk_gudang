<?php

namespace App\Filament\Resources\SupplierReturnInbound;

use App\Filament\Resources\SupplierReturnInbound\Pages\ListSupplierReturnInbounds;
use App\Filament\Resources\SupplierReturnInbound\Schemas\SupplierReturnInboundForm;
use App\Filament\Resources\SupplierReturnInbound\Tables\SupplierReturnInboundsTable;
use App\Models\SupplierReturnInbound;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierReturnInboundResource extends Resource
{
    protected static ?string $model = SupplierReturnInbound::class;

    protected static ?string $slug = 'retur-masuk-supplier';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownOnSquare;

    protected static ?string $navigationLabel = 'Retur Masuk dari Supplier';

    protected static ?string $modelLabel = 'Retur Masuk dari Supplier';

    protected static ?string $pluralModelLabel = 'Retur Masuk dari Supplier';

    protected static string|\UnitEnum|null $navigationGroup = 'Retur';

    public static function form(Schema $schema): Schema
    {
        return SupplierReturnInboundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierReturnInboundsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplierReturnInbounds::route('/'),
        ];
    }
}
