<?php

namespace App\Filament\Resources\KomplainPo;

use App\Filament\Resources\KomplainPo\Pages\ListKomplainPos;
use App\Filament\Resources\KomplainPo\Schemas\KomplainPoForm;
use App\Filament\Resources\KomplainPo\Tables\KomplainPosTable;
use App\Models\KomplainPo;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class KomplainPoResource extends Resource
{
    protected static ?string $model = KomplainPo::class;

    protected static ?string $slug = 'komplain-po';

    protected static ?string $navigationLabel = 'Komplain PO';

    protected static ?string $modelLabel = 'Komplain PO';

    protected static ?string $pluralModelLabel = 'Komplain PO';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Purchasing Order';
    }

    public static function form(Schema $schema): Schema
    {
        return KomplainPoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KomplainPosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKomplainPos::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_komplain_pos') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_komplain_pos') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update_komplain_pos') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete_komplain_pos') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_komplain_pos') ?? false;
    }
}
