<?php

namespace App\Filament\Resources\TaskReturCabangs;

use App\Filament\Resources\TaskReturCabangs\Pages\ListTaskReturCabangs;
use App\Filament\Resources\TaskReturCabangs\Schemas\TaskReturCabangForm;
use App\Filament\Resources\TaskReturCabangs\Tables\TaskReturCabangsTable;
use App\Models\TaskReturCabang;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskReturCabangResource extends Resource
{
    protected static ?string $model = TaskReturCabang::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static ?string $navigationLabel = 'Retur Masuk dari Toko';

    protected static ?string $modelLabel = 'Retur Masuk dari Toko';

    protected static ?string $pluralModelLabel = 'Retur Masuk dari Toko';

    protected static string|\UnitEnum|null $navigationGroup = 'Retur';

    public static function form(Schema $schema): Schema
    {
        return TaskReturCabangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskReturCabangsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('Admin')) {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_task_retur_cabangs') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_task_retur_cabangs') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_task_retur_cabangs') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_task_retur_cabangs') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_task_retur_cabangs') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskReturCabangs::route('/'),
        ];
    }
}
