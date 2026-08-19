<?php

namespace App\Filament\Pages;

use App\Services\BackupService;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class ManageBackups extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.manage-backups';

    protected static ?string $title = 'Backup Database';
    protected static ?string $slug = 'settings/backups';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-circle-stack';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public array $backups = [];

    public function mount(): void
    {
        $this->loadBackups();
        $this->mountInteractsWithTable();
    }

    public function loadBackups(): void
    {
        $this->backups = App::make(BackupService::class)->listBackups();
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => collect($this->backups)->keyBy('name')->all())
            ->striped()
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama File')
                    ->weight('medium')
                    ->grow(false),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'DB + Files' ? 'success' : 'info'),
                TextColumn::make('size')
                    ->label('Ukuran')
                    ->grow(false),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->formatStateUsing(fn (int $state): string => date('d/m/Y H:i:s', $state))
                    ->grow(false),
            ])
            ->emptyStateHeading('Belum ada backup')
            ->emptyStateDescription('Klik "Buat Backup Baru" untuk membuat cadangan database pertama Anda.')
            ->emptyStateIcon('heroicon-o-circle-stack')
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Download')
                    ->action(fn (array $record) => $this->downloadBackup($record['name'])),
                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->requiresConfirmation()
                    ->action(fn (array $record) => $this->deleteBackup($record['name'])),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_backup')
                ->label('Buat Backup Baru')
                ->icon('heroicon-m-plus')
                ->color('success')
                ->form([
                    Toggle::make('include_files')
                        ->label('Sertakan file upload')
                        ->helperText('Sertakan file dari Pusat Dokumen (private) & Foto Komplain (public). Hasil backup berupa file .zip.')
                        ->default(false),
                ])
                ->action(fn (array $data) => $this->runBackup($data['include_files'])),
        ];
    }

    public function runBackup(bool $includeFiles): void
    {
        try {
            $filename = App::make(BackupService::class)->runBackup($includeFiles);
            $this->loadBackups();
            $this->resetTable();

            Notification::make()
                ->title('Backup berhasil dibuat')
                ->body("File: {$filename}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal membuat backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $name)
    {
        try {
            return App::make(BackupService::class)->download($name);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal mengunduh file')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteBackup(string $name): void
    {
        try {
            App::make(BackupService::class)->delete($name);
            $this->loadBackups();
            $this->resetTable();

            Notification::make()
                ->title('Backup berhasil dihapus')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menghapus backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
