<?php

namespace App\Filament\Resources\WarehouseDocument\Pages;

use App\Filament\Resources\WarehouseDocument\Schemas\WarehouseDocumentForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListWarehouseDocuments extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\WarehouseDocument\WarehouseDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Dokumen')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Tambah Dokumen Baru')
                ->modalWidth(Width::Full)
                ->form(WarehouseDocumentForm::getFormFields())
                ->action(function (array $data) {
                    $data['format_file'] = strtolower(pathinfo($data['file_path'], PATHINFO_EXTENSION));
                    $data['user_id'] = auth()->id();
                    $this->getModel()::create($data);
                }),
        ];
    }
}
