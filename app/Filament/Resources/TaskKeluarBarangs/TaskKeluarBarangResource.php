<?php

namespace App\Filament\Resources\TaskKeluarBarangs;

use App\Filament\Resources\TaskKeluarBarangs\Pages\CreateTaskKeluarBarang;
use App\Filament\Resources\TaskKeluarBarangs\Pages\EditTaskKeluarBarang;
use App\Filament\Resources\TaskKeluarBarangs\Pages\ListTaskKeluarBarangs;
use App\Filament\Resources\TaskKeluarBarangs\Schemas\TaskKeluarBarangForm;
use App\Filament\Resources\TaskKeluarBarangs\Tables\TaskKeluarBarangsTable;
use App\Models\TaskKeluarBarang;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskKeluarBarangResource extends Resource
{
    protected static ?string $model = TaskKeluarBarang::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $navigationLabel = 'Keluar Barang';

    protected static ?string $modelLabel = 'Keluar Barang';

    protected static ?string $pluralModelLabel = 'Keluar Barang';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengiriman';

    public static function form(Schema $schema): Schema
    {
        $livewire = $schema->getLivewire();
        
        if ($livewire instanceof CreateTaskKeluarBarang) {
            return $schema->components([
                \Filament\Forms\Components\Repeater::make('tasks')
                    ->schema(TaskKeluarBarangForm::getFormFields())
                    ->label('Daftar Task')
                    ->default([[]])
                    ->reorderable(false)
                    ->addActionLabel('Tambah Baris')
            ]);
        }

        return TaskKeluarBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskKeluarBarangsTable::configure($table);
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
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Keluar');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Keluar');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskKeluarBarangs::route('/'),
            'create' => CreateTaskKeluarBarang::route('/create'),
            'edit' => EditTaskKeluarBarang::route('/{record}/edit'),
        ];
    }
}
