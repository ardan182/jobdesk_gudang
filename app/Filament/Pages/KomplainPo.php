<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class KomplainPo extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Komplain PO';

    protected static string|\UnitEnum|null $navigationGroup = 'Purchasing Order';

    protected static ?string $slug = 'komplain-po';

    protected static ?int $navigationSort = 1;

    public function getView(): string
    {
        return 'filament.pages.komplain-po';
    }
}
