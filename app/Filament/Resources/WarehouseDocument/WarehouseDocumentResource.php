<?php

namespace App\Filament\Resources\WarehouseDocument;

use App\Filament\Resources\WarehouseDocument\Pages\ListWarehouseDocuments;
use App\Filament\Resources\WarehouseDocument\Schemas\WarehouseDocumentForm;
use App\Filament\Resources\WarehouseDocument\Tables\WarehouseDocumentsTable;
use App\Models\WarehouseDocument;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WarehouseDocumentResource extends Resource
{
    protected static ?string $model = WarehouseDocument::class;

    protected static ?string $slug = 'pusat-dokumen';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    protected static ?string $navigationLabel = 'Pusat Dokumen';

    protected static ?string $modelLabel = 'Pusat Dokumen';

    protected static ?string $pluralModelLabel = 'Pusat Dokumen';

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    public static function form(Schema $schema): Schema
    {
        return WarehouseDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehouseDocumentsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_warehouse_documents') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_warehouse_documents') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update_warehouse_documents') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_warehouse_documents') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_warehouse_documents') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWarehouseDocuments::route('/'),
        ];
    }
}
