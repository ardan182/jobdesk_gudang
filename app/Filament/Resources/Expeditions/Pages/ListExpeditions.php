<?php

namespace App\Filament\Resources\Expeditions\Pages;

use App\Filament\Resources\Expeditions\ExpeditionResource;
use App\Filament\Resources\Expeditions\Schemas\ExpeditionForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpeditions extends ListRecords
{
    protected static string $resource = ExpeditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Tambah Ekspedisi')
                ->form(ExpeditionForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
