<?php

namespace App\Filament\Resources\MasterKendaraans\Pages;

use App\Filament\Resources\MasterKendaraans\MasterKendaraanResource;
use App\Filament\Resources\MasterKendaraans\Schemas\MasterKendaraanForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterKendaraans extends ListRecords
{
    protected static string $resource = MasterKendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color("primary")
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Kendaraan')
                ->form(MasterKendaraanForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
