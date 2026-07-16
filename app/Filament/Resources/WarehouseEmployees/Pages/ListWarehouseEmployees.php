<?php

namespace App\Filament\Resources\WarehouseEmployees\Pages;

use App\Filament\Resources\WarehouseEmployees\WarehouseEmployeeResource;
use App\Filament\Resources\WarehouseEmployees\Schemas\WarehouseEmployeeForm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListWarehouseEmployees extends ListRecords
{
    protected static string $resource = WarehouseEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importEmployee')
                ->label('Import Employee')
                ->color('primary')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Import Employee dari Excel')
                ->modalWidth('lg')
                ->form([
                    \Filament\Forms\Components\Placeholder::make('template')
                        ->label('')
                        ->content(new HtmlString('
                            <a href="' . route('employees.template.download') . '" target="_blank"
                               class="text-primary-600 underline text-sm">
                               📥 Download Template Excel (XLSX)
                            </a>
                            <p class="text-gray-500 text-xs mt-1">
                                Bisa dibuka dengan Microsoft Excel, LibreOffice, atau Google Sheets.
                                Kolom Divisi Gudang sudah ada dropdown pilihan.
                            </p>
                        ')),
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File Excel / CSV')
                        ->disk('local')
                        ->directory('imports')
                        ->storeFiles()
                        ->required(),
                ])
                ->action(function (array $data) {
                    // TODO: implement import logic later
                    \Filament\Notifications\Notification::make()
                        ->title('Info')
                        ->body('Fitur import akan segera tersedia.')
                        ->info()
                        ->send();
                }),
            CreateAction::make()
                ->color("primary")
                ->modalHeading('Tambah Master Employee Gudang')
                ->form(WarehouseEmployeeForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
