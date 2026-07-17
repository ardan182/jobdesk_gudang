<?php

namespace App\Filament\Resources\BranchReturnOutbound;

use App\Filament\Resources\BranchReturnOutbound\Pages\ListBranchReturnOutbounds;
use App\Filament\Resources\BranchReturnOutbound\Schemas\BranchReturnOutboundForm;
use App\Filament\Resources\BranchReturnOutbound\Tables\BranchReturnOutboundsTable;
use App\Models\BranchReturnOutbound;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BranchReturnOutboundResource extends Resource
{
    protected static ?string $model = BranchReturnOutbound::class;

    protected static ?string $slug = 'retur-keluar-cabang';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static ?string $navigationLabel = 'Retur Keluar untuk Cabang';

    protected static ?string $modelLabel = 'Retur Keluar untuk Cabang';

    protected static ?string $pluralModelLabel = 'Retur Keluar untuk Cabang';

    protected static string|\UnitEnum|null $navigationGroup = 'Retur';

    public static function form(Schema $schema): Schema
    {
        return BranchReturnOutboundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchReturnOutboundsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranchReturnOutbounds::route('/'),
        ];
    }
}
