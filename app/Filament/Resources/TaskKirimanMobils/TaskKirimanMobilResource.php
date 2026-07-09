<?php

namespace App\Filament\Resources\TaskKirimanMobils;

use App\Filament\Resources\TaskKirimanMobils\Pages\CreateTaskKirimanMobil;
use App\Filament\Resources\TaskKirimanMobils\Pages\EditTaskKirimanMobil;
use App\Filament\Resources\TaskKirimanMobils\Pages\ListTaskKirimanMobils;
use App\Filament\Resources\TaskKirimanMobils\Schemas\TaskKirimanMobilForm;
use App\Filament\Resources\TaskKirimanMobils\Tables\TaskKirimanMobilsTable;
use App\Models\TaskKirimanMobil;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskKirimanMobilResource extends Resource
{
    protected static ?string $model = TaskKirimanMobil::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $navigationLabel = 'Kiriman Mobil';

    protected static ?string $modelLabel = 'Kiriman Mobil';

    protected static ?string $pluralModelLabel = 'Kiriman Mobil';

    protected static string|\UnitEnum|null $navigationGroup = 'Kiriman Cabang';

    public static function form(Schema $schema): Schema
    {
        $livewire = $schema->getLivewire();
        
        if ($livewire instanceof CreateTaskKirimanMobil) {
            return $schema->components([
                \Filament\Forms\Components\Repeater::make('tasks')
                    ->schema(TaskKirimanMobilForm::getFormFields())
                    ->label('Daftar Task')
                    ->default([[]])
                    ->reorderable(false)
                    ->addActionLabel('Tambah Baris')
            ]);
        }

        return TaskKirimanMobilForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaskKirimanMobilsTable::configure($table);
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
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Kiriman');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Kiriman');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaskKirimanMobils::route('/'),
            'create' => CreateTaskKirimanMobil::route('/create'),
            'edit' => EditTaskKirimanMobil::route('/{record}/edit'),
        ];
    }
}
