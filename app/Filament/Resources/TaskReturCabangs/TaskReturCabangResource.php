<?php

namespace App\Filament\Resources\TaskReturCabangs;

use App\Filament\Resources\TaskReturCabangs\Pages\CreateTaskReturCabang;
use App\Filament\Resources\TaskReturCabangs\Pages\EditTaskReturCabang;
use App\Filament\Resources\TaskReturCabangs\Pages\ListTaskReturCabangs;
use App\Filament\Resources\TaskReturCabangs\Schemas\TaskReturCabangForm;
use App\Filament\Resources\TaskReturCabangs\Tables\TaskReturCabangsTable;
use App\Models\TaskReturCabang;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskReturCabangResource extends Resource
{
    protected static ?string $model = TaskReturCabang::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Retur dari Cabang';

    protected static ?string $modelLabel = 'Retur dari Cabang';

    protected static ?string $pluralModelLabel = 'Retur dari Cabang';

    protected static string|\UnitEnum|null $navigationGroup = 'Checker Retur';

    public static function form(Schema $schema): Schema
    {
        $livewire = $schema->getLivewire();
        
        if ($livewire instanceof CreateTaskReturCabang) {
            return $schema->components([
                \Filament\Forms\Components\Repeater::make('tasks')
                    ->schema(TaskReturCabangForm::getFormFields())
                    ->label('Daftar Task')
                    ->default([[]])
                    ->reorderable(false)
                    ->addActionLabel('Tambah Baris')
            ]);
        }

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
            'index' => ListTaskReturCabangs::route('/'),
            'create' => CreateTaskReturCabang::route('/create'),
            'edit' => EditTaskReturCabang::route('/{record}/edit'),
        ];
    }
}
