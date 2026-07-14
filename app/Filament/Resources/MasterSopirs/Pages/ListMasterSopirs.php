<?php

namespace App\Filament\Resources\MasterSopirs\Pages;

use App\Filament\Resources\MasterSopirs\MasterSopirResource;
use App\Filament\Resources\MasterSopirs\Schemas\MasterSopirForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterSopirs extends ListRecords
{
    protected static string $resource = MasterSopirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color("primary")
                ->modalHeading('Tambah Master Sopir')
                ->form(MasterSopirForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
