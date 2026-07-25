<?php

namespace App\Filament\Resources\SupplierSj\Pages;

use App\Filament\Resources\SupplierSj\SupplierSjResource;
use Filament\Resources\Pages\ListRecords;

class ListSupplierSjs extends ListRecords
{
    protected static string $resource = SupplierSjResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
