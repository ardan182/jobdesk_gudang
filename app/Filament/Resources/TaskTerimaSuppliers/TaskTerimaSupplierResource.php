<?php

namespace App\Filament\Resources\TaskTerimaSuppliers;

use App\Filament\Resources\TaskTerimaSuppliers\Pages\CreateTaskTerimaSupplier;
use App\Filament\Resources\TaskTerimaSuppliers\Pages\ListTaskTerimaSuppliers;
use App\Filament\Resources\TaskTerimaSuppliers\Schemas\TaskTerimaSupplierForm;
use App\Filament\Resources\TaskTerimaSuppliers\Tables\TaskTerimaSuppliersTable;
use App\Models\TaskTerimaSupplier;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskTerimaSupplierResource extends Resource
{
    protected static ?string $model = TaskTerimaSupplier::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Checker Terima Barang Supplier';

    protected static ?string $modelLabel = 'Checker Terima Barang Supplier';

    protected static ?string $pluralModelLabel = 'Checker Terima Barang Supplier';

    protected static string|\UnitEnum|null $navigationGroup = 'Penerimaan';

    public static function form(Schema $schema): Schema
    {
        $livewire = $schema->getLivewire();

        if ($livewire instanceof CreateTaskTerimaSupplier) {
            return $schema->components([
                \Filament\Forms\Components\Repeater::make('tasks')
                    ->schema(TaskTerimaSupplierForm::getFormFields())
                    ->label('Daftar Task')
                    ->default([[]])
                    ->reorderable(false)
                    ->addActionLabel('Tambah Baris'),
            ]);
        }

        return TaskTerimaSupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskTerimaSuppliersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('helpers');

        if (auth()->user()?->hasRole('Admin')) {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Terima');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Terima');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskTerimaSuppliers::route('/'),
            'create' => CreateTaskTerimaSupplier::route('/create'),
        ];
    }
}
