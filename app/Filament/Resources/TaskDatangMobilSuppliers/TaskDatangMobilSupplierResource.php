<?php

namespace App\Filament\Resources\TaskDatangMobilSuppliers;

use App\Filament\Resources\TaskDatangMobilSuppliers\Pages\CreateTaskDatangMobilSupplier;
use App\Filament\Resources\TaskDatangMobilSuppliers\Pages\ListTaskDatangMobilSuppliers;
use App\Filament\Resources\TaskDatangMobilSuppliers\Schemas\TaskDatangMobilSupplierForm;
use App\Filament\Resources\TaskDatangMobilSuppliers\Tables\TaskDatangMobilSuppliersTable;
use App\Models\ArrivalSupplierTruck;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaskDatangMobilSupplierResource extends Resource
{
    protected static ?string $model = ArrivalSupplierTruck::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Datang Mobil Supplier';

    protected static ?string $modelLabel = 'Datang Mobil Supplier';

    protected static ?string $pluralModelLabel = 'Datang Mobil Supplier';

    protected static string|\UnitEnum|null $navigationGroup = 'Penerimaan';

    public static function form(Schema $schema): Schema
    {
        $livewire = $schema->getLivewire();

        if ($livewire instanceof CreateTaskDatangMobilSupplier) {
            return $schema->components([
                \Filament\Forms\Components\Repeater::make('tasks')
                    ->schema(TaskDatangMobilSupplierForm::getFormFields())
                    ->label('Daftar Task')
                    ->default([[]])
                    ->reorderable(false)
                    ->addActionLabel('Tambah Baris'),
            ]);
        }

        return TaskDatangMobilSupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskDatangMobilSuppliersTable::configure($table);
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
            'index' => ListTaskDatangMobilSuppliers::route('/'),
            'create' => CreateTaskDatangMobilSupplier::route('/create'),
        ];
    }
}
