<?php

namespace App\Filament\Resources\TaskKeluarBarangs;

use App\Filament\Resources\TaskKeluarBarangs\Pages\ListTaskKeluarBarangs;
use App\Filament\Resources\TaskKeluarBarangs\Schemas\TaskKeluarBarangForm;
use App\Filament\Resources\TaskKeluarBarangs\Tables\TaskKeluarBarangsTable;
use App\Models\TaskKeluarBarang;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskKeluarBarangResource extends Resource
{
    protected static ?string $model = TaskKeluarBarang::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Checker Keluar Barang';

    protected static ?string $modelLabel = 'Checker Keluar Barang';

    protected static ?string $pluralModelLabel = 'Checker Keluar Barang';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengiriman';

    public static function form(Schema $schema): Schema
    {
        return TaskKeluarBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskKeluarBarangsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->can('view_all_data')) {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_task_keluar_barangs') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_task_keluar_barangs') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_task_keluar_barangs') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_task_keluar_barangs') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_task_keluar_barangs') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskKeluarBarangs::route('/'),
        ];
    }
}
