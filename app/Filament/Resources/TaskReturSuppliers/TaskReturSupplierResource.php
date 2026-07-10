<?php

namespace App\Filament\Resources\TaskReturSuppliers;

use App\Filament\Resources\TaskReturSuppliers\Pages\CreateTaskReturSupplier;
use App\Filament\Resources\TaskReturSuppliers\Pages\EditTaskReturSupplier;
use App\Filament\Resources\TaskReturSuppliers\Pages\ListTaskReturSuppliers;
use App\Filament\Resources\TaskReturSuppliers\Schemas\TaskReturSupplierForm;
use App\Filament\Resources\TaskReturSuppliers\Tables\TaskReturSuppliersTable;
use App\Models\TaskReturSupplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;

class TaskReturSupplierResource extends Resource
{
    protected static ?string $model = TaskReturSupplier::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Retur ke Supplier';

    protected static ?string $modelLabel = 'Retur ke Supplier';

    protected static ?string $pluralModelLabel = 'Retur ke Supplier';

    protected static string|\UnitEnum|null $navigationGroup = 'Retur';

    public static function form(Schema $schema): Schema
    {
        $livewire = $schema->getLivewire();

        if ($livewire instanceof CreateTaskReturSupplier) {
            return $schema->components([
                \Filament\Forms\Components\Repeater::make('tasks')
                    ->schema(TaskReturSupplierForm::getFormFields())
                    ->label('Daftar Task')
                    ->default([[]])
                    ->reorderable(false)
                    ->addActionLabel('Tambah Baris'),
            ]);
        }

        return TaskReturSupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskReturSuppliersTable::configure($table);
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
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Retur');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Retur');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskReturSuppliers::route('/'),
            'create' => CreateTaskReturSupplier::route('/create'),
            'edit' => EditTaskReturSupplier::route('/{record}/edit'),
        ];
    }
}
