<?php

namespace App\Filament\Resources\BranchShipment;

use App\Filament\Resources\BranchShipment\Pages\ListBranchShipments;
use App\Filament\Resources\BranchShipment\Schemas\BranchShipmentForm;
use App\Filament\Resources\BranchShipment\Tables\BranchShipmentsTable;
use App\Models\BranchShipment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BranchShipmentResource extends Resource
{
    protected static ?string $model = BranchShipment::class;

    protected static ?string $slug = 'input-kirim-barang';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static ?string $navigationLabel = 'Input Kirim Barang';

    protected static ?string $modelLabel = 'Input Kirim Barang';

    protected static ?string $pluralModelLabel = 'Input Kirim Barang';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengiriman';

    public static function form(Schema $schema): Schema
    {
        return BranchShipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchShipmentsTable::configure($table);
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

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('Admin') || $user?->hasRole('Checker Keluar');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranchShipments::route('/'),
        ];
    }
}
