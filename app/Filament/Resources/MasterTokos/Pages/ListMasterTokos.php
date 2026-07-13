<?php

namespace App\Filament\Resources\MasterTokos\Pages;

use App\Filament\Resources\MasterTokos\MasterTokoResource;
use App\Filament\Resources\MasterTokos\Schemas\MasterTokoForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterTokos extends ListRecords
{
    protected static string $resource = MasterTokoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Tambah Master Toko')
                ->form(MasterTokoForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
