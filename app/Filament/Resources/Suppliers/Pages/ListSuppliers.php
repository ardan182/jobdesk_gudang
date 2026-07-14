<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Exports\SupplierTemplateExport;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Resources\Suppliers\Schemas\SupplierForm;
use App\Imports\SupplierImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importSupplier')
                ->label('Import Supplier')
                ->color('primary')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Import Supplier dari CSV')
                ->modalWidth('lg')
                ->form([
                    \Filament\Forms\Components\Placeholder::make('template')
                        ->label('')
                        ->content(new HtmlString('
                            <a href="' . route('suppliers.template.download') . '" target="_blank"
                               class="text-primary-600 underline text-sm">
                               📥 Download Template Excel (XLS)
                            </a>
                            <p class="text-gray-500 text-xs mt-1">
                                Bisa dibuka dengan Microsoft Excel, LibreOffice, atau Google Sheets.
                            </p>
                        ')),
                    FileUpload::make('file')
                        ->label('File Excel / CSV')
                        ->disk('local')
                        ->directory('imports')
                        ->storeFiles()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $file = $data['file'];

                    if ($file instanceof TemporaryUploadedFile) {
                        $path = $file->store('imports');
                        $fullPath = Storage::disk('local')->path($path);
                    } else {
                        $fullPath = Storage::disk('local')->path($file);
                    }

                    $import = new SupplierImport();
                    $result = $import->import($fullPath);

                    if ($result['error']) {
                        Notification::make()
                            ->title('Import gagal')
                            ->body($result['error'])
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Import selesai')
                        ->body("{$result['success']} data berhasil ditambahkan. {$result['skipped']} data dilewati (duplikat).")
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->color("primary")
                ->modalHeading('Tambah Supplier')
                ->form(SupplierForm::getFormFields())
                ->modalWidth('lg'),
        ];
    }
}
