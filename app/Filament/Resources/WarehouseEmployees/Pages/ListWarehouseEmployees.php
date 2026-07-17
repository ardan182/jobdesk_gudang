<?php

namespace App\Filament\Resources\WarehouseEmployees\Pages;

use App\Filament\Resources\WarehouseEmployees\Schemas\WarehouseEmployeeForm;
use App\Filament\Resources\WarehouseEmployees\WarehouseEmployeeResource;
use App\Filament\Resources\WarehouseEmployees\Widgets\DivisionsTableWidget;
use App\Imports\WarehouseEmployeeImport;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListWarehouseEmployees extends ListRecords
{
    protected static string $resource = WarehouseEmployeeResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Employee')
                            ->label('Employee')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Section::make('Daftar Employee')
                                    ->headerActions([
                                        Action::make('importEmployee')
                                            ->label('Import Employee')
                                            ->color('primary')
                                            ->icon('heroicon-o-arrow-up-tray')
                                            ->modalHeading('Import Employee dari Excel')
                                            ->modalWidth('lg')
                                            ->form([
                                                Placeholder::make('template')
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

                                                $import = new WarehouseEmployeeImport();
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
                                                    ->body("{$result['success']} data berhasil ditambahkan. {$result['skipped']} data dilewati (duplikat / tidak valid).")
                                                    ->success()
                                                    ->send();
                                            }),
                                        Action::make('create')
                                            ->label('Buat Master Employee Gudang')
                                            ->color('primary')
                                            ->icon('heroicon-m-plus')
                                            ->modalHeading('Tambah Master Employee Gudang')
                                            ->form(WarehouseEmployeeForm::getFormFields())
                                            ->modalWidth('lg')
                                            ->action(function (array $data) {
                                                $this->getModel()::create($data);
                                            }),
                                    ])
                                    ->schema([
                                        RenderHook::make('panels::resource.pages.list-records.table.before'),
                                        EmbeddedTable::make(),
                                        RenderHook::make('panels::resource.pages.list-records.table.after'),
                                    ]),
                            ]),
                        Tab::make('Divisi')
                            ->label('Divisi')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Livewire::make(DivisionsTableWidget::class),
                            ]),
                    ]),
            ]);
    }
}
