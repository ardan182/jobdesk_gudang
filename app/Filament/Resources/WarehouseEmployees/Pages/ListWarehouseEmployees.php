<?php

namespace App\Filament\Resources\WarehouseEmployees\Pages;

use App\Filament\Resources\WarehouseEmployees\WarehouseEmployeeResource;
use App\Filament\Resources\WarehouseEmployees\Schemas\WarehouseEmployeeForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseEmployees extends ListRecords
{
    protected static string $resource = WarehouseEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color("primary")
                ->modalHeading('Tambah Master Employee Gudang')
                ->form(WarehouseEmployeeForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
