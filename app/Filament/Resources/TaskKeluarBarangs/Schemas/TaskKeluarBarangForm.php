<?php

namespace App\Filament\Resources\TaskKeluarBarangs\Schemas;

use App\Models\BranchShipment;
use App\Models\WarehouseEmployee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskKeluarBarangForm
{
    public static function getFormFields(): array
    {
        return [
            Section::make('Informasi Task')
                ->columns(2)
                ->schema([
                    Select::make('branch_shipment_id')
                        ->label('Ambil dari Kiriman Barang')
                        ->disabled(fn ($record) => $record !== null)
                        ->options(function ($record) {
                            $query = BranchShipment::where('status', 'selesai')
                                ->whereNotIn('id', function ($q) use ($record) {
                                    $q->select('branch_shipment_id')
                                        ->from('task_keluar_barangs')
                                        ->whereNotNull('branch_shipment_id');
                                    if ($record && $record->id) {
                                        $q->where('id', '!=', $record->id);
                                    }
                                });
                            return $query->get()->mapWithKeys(fn ($item) => [
                                $item->id => "{$item->cabang} - {$item->nomor_sj} - {$item->total_qty} qty"
                            ]);
                        })
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih kiriman barang...')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) {
                                $set('cabang', null);
                                $set('nomor_sj', null);
                                $set('total_qty', null);
                                $set('no_po', null);
                                return;
                            }
                            $shipment = BranchShipment::find($state);
                            if ($shipment) {
                                $set('cabang', $shipment->cabang);
                                $set('nomor_sj', $shipment->nomor_sj);
                                $set('total_qty', $shipment->total_qty);
                                $set('no_po', $shipment->no_po);
                            }
                        }),
                    TextInput::make('cabang')
                        ->label('Nama Cabang')
                        ->disabled(),
                    TextInput::make('nomor_sj')
                        ->label('Nomor SJ')
                        ->disabled(),
                    TextInput::make('total_qty')
                        ->label('Total Qty')
                        ->disabled(),
                    TextInput::make('no_po')
                        ->label('No PO')
                        ->disabled(),
                    TimePicker::make('jam_disiapkan')
                        ->label('Jam Disiapkan')
                        ->seconds(false)
                        ->step(60)
                        ->extraAttributes(['lang' => 'id-ID'])
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'siap kirim' => 'Siap Kirim',
                            'selesai' => 'Selesai',
                        ])
                        ->default('draft')
                        ->required()
                        ->live(),
                    TextInput::make('diserahkan_kepada')
                        ->label('Diserahkan Kepada')
                        ->placeholder('Nama koordinator...'),
                    Select::make('helper')
                        ->label('Helper')
                        ->options(WarehouseEmployee::pluck('nama_karyawan', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih helper...'),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull()
                        ->placeholder('Kondisi proses...'),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getFormFields());
    }
}
