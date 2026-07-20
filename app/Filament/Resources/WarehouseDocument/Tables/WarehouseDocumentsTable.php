<?php

namespace App\Filament\Resources\WarehouseDocument\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class WarehouseDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->columns([
                TextColumn::make('nama_dokumen')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Formulir Lapangan' => 'info',
                        'SOP Gudang' => 'warning',
                        'Template Import' => 'success',
                        default => 'gray',
                    })
                    ->grow(false),
                TextColumn::make('versi')
                    ->label('Versi')
                    ->grow(false),
                TextColumn::make('format_file')
                    ->label('Format')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pdf' => 'danger',
                        'xlsx', 'xls', 'csv', 'ods' => 'success',
                        'doc', 'docx' => 'info',
                        'jpg', 'jpeg', 'png' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->grow(false),
                TextColumn::make('download_count')
                    ->label('Download')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Dokumen')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Dokumen')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('nama_dokumen')->label('Nama Dokumen'),
                                TextEntry::make('kategori')->label('Kategori')->badge(),
                                TextEntry::make('versi')->label('Versi'),
                                TextEntry::make('format_file')->label('Format')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                                TextEntry::make('download_count')->label('Download'),
                                TextEntry::make('user.name')->label('Diupload Oleh'),
                                TextEntry::make('created_at')->label('Tanggal')->date('d/m/Y'),
                                TextEntry::make('deskripsi')->label('Deskripsi')->columnSpanFull(),
                            ]),
                    ]),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->iconButton()
                    ->tooltip('Download Dokumen')
                    ->action(function ($record) {
                        $record->increment('download_count');
                        return Storage::disk('local')->download($record->file_path);
                    }),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->color('danger'),
                ]),
            ]);
    }
}
