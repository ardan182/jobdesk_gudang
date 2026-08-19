<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use PDO;
use Exception;

class BackupService
{
    protected string $disk = 'backups';

    public function listBackups(): array
    {
        if (!Storage::disk($this->disk)->exists('')) {
            return [];
        }

        $files = Storage::disk($this->disk)->files();
        $backups = [];

        foreach ($files as $file) {
            if ($file === '.gitignore') {
                continue;
            }

            $path = Storage::disk($this->disk)->path($file);
            if (!File::exists($path)) {
                continue;
            }
            
            $backups[] = [
                'name' => $file,
                'size' => $this->formatBytes(filesize($path)),
                'raw_size' => filesize($path),
                'created_at' => filemtime($path),
                'type' => str_ends_with($file, '.zip') ? 'DB + Files' : 'DB Only',
            ];
        }

        // Sort by created_at desc
        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    public function runBackup(bool $includeFiles = false): string
    {
        $backupDir = Storage::disk($this->disk)->path('');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd-His');
        
        if ($includeFiles) {
            $zipName = "backup-{$timestamp}.zip";
            $zipPath = Storage::disk($this->disk)->path($zipName);
            
            // Create temp SQL file (uncompressed)
            $sqlName = "temp-db-{$timestamp}.sql";
            $sqlPath = Storage::disk($this->disk)->path($sqlName);
            
            $this->dumpDatabase($sqlPath, false);
            
            $this->createZip($zipPath, $sqlPath);
            
            // Delete temp SQL
            File::delete($sqlPath);
            
            $this->prune();
            return $zipName;
        } else {
            $sqlName = "backup-{$timestamp}.sql.gz";
            $sqlPath = Storage::disk($this->disk)->path($sqlName);
            
            $this->dumpDatabase($sqlPath, true);
            
            $this->prune();
            return $sqlName;
        }
    }

    protected function dumpDatabase(string $outputPath, bool $compress = false): void
    {
        $pdo = DB::connection()->getPdo();
        
        // Force PDO to return associative arrays
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $fp = $compress ? gzopen($outputPath, 'w9') : fopen($outputPath, 'w');
        if (!$fp) {
            throw new Exception("Cannot open file: {$outputPath} for writing.");
        }

        $write = function($data) use ($fp, $compress) {
            if ($compress) {
                gzwrite($fp, $data);
            } else {
                fwrite($fp, $data);
            }
        };

        // Write header
        $write("-- Database Backup\n");
        $write("-- Created: " . date('Y-m-d H:i:s') . "\n");
        $write("-- Host: " . config('database.connections.mysql.host') . "\n");
        $write("-- Database: " . config('database.connections.mysql.database') . "\n\n");
        
        $write("SET FOREIGN_KEY_CHECKS=0;\n");
        $write("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        $write("SET time_zone = \"+00:00\";\n\n");

        $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Get create table statement
            $createStatement = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
            $createTableSql = $createStatement[1] ?? '';

            $write("-- --------------------------------------------------------\n");
            $write("-- Table structure for table `{$table}`\n");
            $write("-- --------------------------------------------------------\n\n");
            $write("DROP TABLE IF EXISTS `{$table}`;\n");
            $write("{$createTableSql};\n\n");

            // Dump data
            $write("-- Dump data for table `{$table}`\n\n");
            
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $columnCount = $stmt->columnCount();
            
            $cols = [];
            for ($i = 0; $i < $columnCount; $i++) {
                $meta = $stmt->getColumnMeta($i);
                $cols[] = $meta['name'];
            }
            
            $colNames = implode('`, `', $cols);
            
            $rows = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $escaped = [];
                foreach ($cols as $col) {
                    $val = $row[$col];
                    if (is_null($val)) {
                        $escaped[] = 'NULL';
                    } else {
                        $escaped[] = $pdo->quote($val);
                    }
                }
                $rows[] = '(' . implode(', ', $escaped) . ')';
                
                if (count($rows) >= 200) {
                    $write("INSERT INTO `{$table}` (`{$colNames}`) VALUES\n" . implode(",\n", $rows) . ";\n");
                    $rows = [];
                }
            }
            
            if (count($rows) > 0) {
                $write("INSERT INTO `{$table}` (`{$colNames}`) VALUES\n" . implode(",\n", $rows) . ";\n");
            }
            
            $write("\n");
        }

        $write("SET FOREIGN_KEY_CHECKS=1;\n");
        
        if ($compress) {
            gzclose($fp);
        } else {
            fclose($fp);
        }
    }

    protected function createZip(string $zipPath, string $sqlPath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create zip archive: {$zipPath}");
        }

        // Add SQL dump at the root of zip
        $zip->addFile($sqlPath, basename($sqlPath));

        // Add private storage uploads (document_templates)
        $privatePath = storage_path('app/private');
        if (File::exists($privatePath)) {
            $this->addDirectoryToZip($zip, $privatePath, 'storage/private');
        }

        // Add public storage uploads (fotos-komplain)
        $publicPath = storage_path('app/public');
        if (File::exists($publicPath)) {
            $this->addDirectoryToZip($zip, $publicPath, 'storage/public');
        }

        $zip->close();
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dirPath, string $zipDirName): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                
                // Exclude temporary/unneeded directories inside private/public
                if (str_contains($filePath, 'livewire-tmp')) {
                    continue;
                }

                $relativePath = substr($filePath, strlen($dirPath) + 1);
                $zipPath = $zipDirName . '/' . str_replace('\\', '/', $relativePath);

                $zip->addFile($filePath, $zipPath);
            }
        }
    }

    public function prune(int $keep = 10): void
    {
        $backups = $this->listBackups();
        if (count($backups) <= $keep) {
            return;
        }

        $toDelete = array_slice($backups, $keep);
        foreach ($toDelete as $backup) {
            Storage::disk($this->disk)->delete($backup['name']);
        }
    }

    public function delete(string $filename): void
    {
        Storage::disk($this->disk)->delete($filename);
    }

    public function download(string $filename)
    {
        return Storage::disk($this->disk)->download($filename);
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
