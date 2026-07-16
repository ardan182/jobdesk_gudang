<?php

namespace App\Filament\Resources\WarehouseEmployees\Widgets;

use App\Models\Division;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DivisionsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Daftar Divisi';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Division::query())
            ->columns([
                TextColumn::make('nama_divisi')
                    ->label('Nama Divisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(60),
                TextColumn::make('employees_count')
                    ->label('Jml Karyawan')
                    ->counts('employees')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make('tambahDivisi')
                    ->label('Tambah Divisi')
                    ->modalHeading('Tambah Divisi')
                    ->modalWidth('lg')
                    ->form([
                        TextInput::make('nama_divisi')
                            ->label('Nama Divisi')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3),
                    ])
                    ->action(function (array $data) {
                        Division::create($data);
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat'),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah')
                    ->modalWidth('lg')
                    ->form([
                        TextInput::make('nama_divisi')
                            ->label('Nama Divisi')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3),
                    ]),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus'),
            ]);
    }
}
