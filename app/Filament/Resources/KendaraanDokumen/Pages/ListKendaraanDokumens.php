<?php

namespace App\Filament\Resources\KendaraanDokumen\Pages;

use App\Filament\Resources\KendaraanDokumen\KendaraanDokumenResource;
use Filament\Resources\Pages\ListRecords;

class ListKendaraanDokumens extends ListRecords
{
    protected static string $resource = KendaraanDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
