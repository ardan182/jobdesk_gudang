<?php

namespace App\Filament\Resources\TaskReturCabangs\Tables;

use App\Filament\Resources\TaskReturCabangs\Schemas\TaskReturCabangForm;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class TaskReturCabangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id_task')
                    ->label('ID Task')
                    ->searchable()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('cabang')
                    ->label('Toko')
                    ->searchable()
                    ->width('130px')
                    ->grow(false),
                TextColumn::make('no_plat_mobil')
                    ->label('No Plat')
                    ->width('120px')
                    ->grow(false),
                TextColumn::make('jam_tiba')
                    ->label('Jam Tiba')
                    ->time('H:i')
                    ->grow(false),
                TextColumn::make('jenis_retur')
                    ->label('Jenis Retur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'retur_jelek' => 'danger',
                        'retur_bagus' => 'success',
                        'rb_dan_rj' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'retur_jelek' => 'Retur Jelek',
                        'retur_bagus' => 'Retur Bagus',
                        'rb_dan_rj' => 'RB dan RJ',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('tanggal_bongkar')
                    ->label('Tgl Bongkar')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jam_bongkar')
                    ->label('Jam Bongkar')
                    ->time('H:i')
                    ->sortable()
                    ->grow(false),
                TextColumn::make('jumlah_sj')
                    ->label('Jumlah SJ')
                    ->numeric()
                    ->sortable()
                    ->grow(false),
                TextColumn::make('nama_sopir')
                    ->label('Sopir')
                    ->searchable()
                    ->grow(false),
                TextColumn::make('helpers_list')
                    ->label('Helper')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn ($record) => $record->helpers->pluck('nama_karyawan')->toArray())
                    ->grow(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->grow(false),
                TextColumn::make('user.name')
                    ->label('Checker')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false)
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->grow(false),
            ])
            ->filters([
                Filter::make('cabang')
                    ->label('Toko')
                    ->form([
                        Select::make('cabang')
                            ->label('Toko')
                            ->options(fn () => \App\Models\TaskKirimanMobil::whereIn('retur_option', ['ada_retur'])
                                ->pluck('cabang', 'cabang')->unique())
                            ->placeholder('Semua Toko'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['cabang'] ?? null,
                        fn (Builder $query, $cabang): Builder => $query->where('cabang', $cabang),
                    )),
                Filter::make('jenis_retur')
                    ->label('Jenis Retur')
                    ->form([
                        Select::make('jenis_retur')
                            ->label('Jenis Retur')
                            ->options([
                                'retur_bagus' => 'Retur Bagus',
                                'retur_jelek' => 'Retur Jelek',
                            ])
                            ->placeholder('Semua'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['jenis_retur'] ?? null,
                        fn (Builder $query, $j): Builder => $query->where('jenis_retur', $j),
                    )),
                Filter::make('status')
                    ->label('Status')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'selesai' => 'Selesai',
                            ])
                            ->placeholder('Semua Status'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['status'] ?? null,
                        fn (Builder $query, $s): Builder => $query->where('status', $s),
                    )),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->modalHeading('Detail Retur Cabang')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn (Action $action) => $action->label('Tutup'))
                    ->schema([
                        Section::make('Informasi Task')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('id_task')->label('ID Task'),
                                TextEntry::make('cabang')->label('Toko'),
                                TextEntry::make('no_plat_mobil')->label('No Plat'),
                                TextEntry::make('jam_tiba')->label('Jam Tiba'),
                                TextEntry::make('jenis_retur')->label('Jenis Retur')->badge(),
                                TextEntry::make('tanggal_bongkar')->label('Tanggal Bongkar'),
                                TextEntry::make('jam_bongkar')->label('Jam Bongkar'),
                                TextEntry::make('jumlah_sj')->label('Jumlah SJ'),
                                TextEntry::make('nama_sopir')->label('Sopir'),
                                TextEntry::make('helpers_list')
                                    ->label('Helper')
                                    ->badge()
                                    ->color('info')
                                    ->state(fn ($record) => $record->helpers->pluck('nama_karyawan')->toArray()),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Ubah Data')
                    ->color('warning')
                    ->modalWidth(Width::Full)
                    ->form(TaskReturCabangForm::getFormFields())
                    ->using(function ($record, array $data) {
                        $helpers = $data['helpers'] ?? [];
                        unset($data['helpers'], $data['kiriman_mobil_id']);
                        $record->update($data);
                        $record->helpers()->sync(filled($helpers) ? $helpers : []);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->iconButton()
                        ->tooltip('Hapus Data')
                        ->color('danger')
                        ->visible(fn () => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}
