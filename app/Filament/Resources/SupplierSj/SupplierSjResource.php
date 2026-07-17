<?php

namespace App\Filament\Resources\SupplierSj;

use App\Filament\Resources\SupplierSj\Pages\ListSupplierSjs;
use App\Filament\Resources\SupplierSj\Schemas\SupplierSjForm;
use App\Filament\Resources\SupplierSj\Tables\SupplierSjsTable;
use App\Models\SupplierSj;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierSjResource extends Resource
{
    protected static ?string $model = SupplierSj::class;

    protected static ?string $slug = 'input-sj-supplier';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentPlus;

    protected static ?string $navigationLabel = 'INPUT SJ dari SUPLIER';

    protected static ?string $modelLabel = 'Input SJ dari Suplier';

    protected static ?string $pluralModelLabel = 'Input SJ dari Suplier';

    protected static string|\UnitEnum|null $navigationGroup = 'Penerimaan';

    public static function form(Schema $schema): Schema
    {
        return SupplierSjForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierSjsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplierSjs::route('/'),
        ];
    }
}
