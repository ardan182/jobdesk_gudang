<?php

namespace App\Filament\Resources\BranchShipment\Pages;

use App\Filament\Resources\BranchShipment\BranchShipmentResource;
use App\Filament\Resources\BranchShipment\Schemas\BranchShipmentForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListBranchShipments extends ListRecords
{
    protected static string $resource = BranchShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Kirim Barang')
                ->form(BranchShipmentForm::getFormFields())
                ->modalWidth(Width::Full),
        ];
    }
}
