<?php

namespace App\Filament\Resources\MasterMobils\Pages;

use App\Filament\Resources\MasterMobils\MasterMobilResource;
use App\Filament\Resources\MasterMobils\Schemas\MasterMobilForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterMobils extends ListRecords
{
    protected static string $resource = MasterMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Tambah Master Mobil')
                ->form(MasterMobilForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
