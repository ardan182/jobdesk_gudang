<?php

namespace App\Filament\Resources\SupplierSj\Pages;

use App\Filament\Resources\SupplierSj\SupplierSjResource;
use App\Models\TaskTerimaSupplier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSupplierSjs extends ListRecords
{
    protected static string $resource = SupplierSjResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Input SJ Baru')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->modalHeading('Input SJ Baru')
                ->modalWidth('lg')
                ->form(\App\Filament\Resources\SupplierSj\Schemas\SupplierSjForm::getFormFields())
                ->action(function (array $data) {
                    if (($data['status_input'] ?? null) === 'selesai' && blank($data['nomor_po_referensi'] ?? null)) {
                        Notification::make()
                            ->title('Gagal menyimpan')
                            ->body('No PO Referensi wajib diisi jika status "Selesai".')
                            ->danger()
                            ->send();
                        return;
                    }

                    $record = $this->getModel()::create($data);

                    if (filled($record->nomor_po_referensi)) {
                        static::syncPoToTerimaSupplier($record);
                    }
                }),
        ];
    }

    public static function syncPoToTerimaSupplier($record): void
    {
        preg_match('/\bTRM-SUP-\d+\b/', $record->keterangan ?? '', $m);
        if (empty($m[0])) return;

        $terima = TaskTerimaSupplier::where('id_task', $m[0])->first();
        if ($terima && blank($terima->no_po_referensi)) {
            $terima->update(['no_po_referensi' => $record->nomor_po_referensi]);
        }
    }
}
