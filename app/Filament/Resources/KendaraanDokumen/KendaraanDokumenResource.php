<?php

namespace App\Filament\Resources\KendaraanDokumen;

use App\Filament\Resources\KendaraanDokumen\Pages\ListKendaraanDokumens;
use App\Filament\Resources\KendaraanDokumen\Schemas\KendaraanDokumenForm;
use App\Filament\Resources\KendaraanDokumen\Tables\KendaraanDokumensTable;
use App\Models\KendaraanDokumen;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class KendaraanDokumenResource extends Resource
{
    protected static ?string $model = KendaraanDokumen::class;

    protected static ?string $navigationLabel = 'Masa Berlaku STNK/KIR';

    protected static ?string $modelLabel = 'Masa Berlaku STNK/KIR';

    protected static ?string $pluralModelLabel = 'Masa Berlaku STNK/KIR';

    protected static ?string $slug = 'masa-berlaku-stnk-kir';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administrasi';
    }

    public static function form(Schema $schema): Schema
    {
        return KendaraanDokumenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KendaraanDokumensTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKendaraanDokumens::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_kendaraan_dokumens') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_kendaraan_dokumens') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_kendaraan_dokumens') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_kendaraan_dokumens') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_kendaraan_dokumens') ?? false;
    }
}
