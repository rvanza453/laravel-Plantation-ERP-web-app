<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SystemMaintenanceController extends Controller
{
    private const FILE = 'modules_statuses.json';

    private const MODULE_KEY_MAP = [
        'sas' => 'ServiceAgreementSystem',
        'qc' => 'QcComplaintSystem',
        'ispo' => 'SystemISPO',
        'management' => 'management',
        'pr' => 'PrSystem',
        'systemsupport' => 'SystemSupport',
        'lab' => 'LabSystem',
    ];

    public function index()
    {
        $status = self::getStatus();
        return view('admin.maintenance.index', compact('status'));
    }

    public function toggle(Request $request, $module)
    {
        $status = self::getStatus();
        $storageKey = self::storageKey($module);
        $status[$storageKey] = !($status[$storageKey] ?? true);
        self::persistStatus($status);
        return back()->with('success', 'Status eksekusi sistem berhasil diubah. Sistem yang mati tidak akan bisa diklik/diakses user di Module Hub.');
    }

    public function runTool(Request $request)
    {
        $validated = $request->validate([
            'admin_password' => 'required|string',
            'action' => 'required|string|in:clear_cache,reset_warehouse,run_build',
        ]);

        $verificationPassword = (string) config('prsystem.app.admin_verification_password', config('app.admin_verification_password'));

        if (($validated['admin_password'] ?? '') !== $verificationPassword) {
            return back()->with('error', 'Password verifikasi salah!');
        }

        if ($validated['action'] === 'clear_cache') {
            Artisan::call('optimize:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            File::deleteDirectory(storage_path('framework/cache/data'));
            File::ensureDirectoryExists(storage_path('framework/cache/data'));
            File::deleteDirectory(storage_path('framework/views'));
            File::ensureDirectoryExists(storage_path('framework/views'));

            return back()->with('success', 'Cache Laravel berhasil dibersihkan dari halaman admin.');
        }

        if ($validated['action'] === 'run_build') {
            // 1. Matikan batasan waktu eksekusi PHP agar proses build tidak terpotong di tengah jalan
            set_time_limit(0);

            // 2. Gunakan absolute path NPM. Ubah '/usr/local/bin/npm' sesuai hasil perintah 'which npm' di terminal Anda.
            // Atau gunakan trik export PATH seperti di bawah ini agar sistem mencari sendiri.
            $command = 'export PATH=$PATH:/usr/local/bin:/usr/bin:/bin && npm run build';

            $process = Process::fromShellCommandline($command, base_path());
            $process->setTimeout(null);
            
            try {
                $process->run();

                if (! $process->isSuccessful()) {
                    // Catat error ke file log Laravel jika output terlalu panjang untuk session alert
                    \Illuminate\Support\Facades\Log::error('NPM Build Failed: ' . $process->getErrorOutput());
                    
                    return back()->with('error', 'npm run build gagal dijalankan: ' . trim($process->getErrorOutput() ?: 'Unknown error. Cek file log Laravel.'));
                }

                return back()->with('success', 'npm run build berhasil dieksekusi dengan baik.');
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Process Run Exception: ' . $e->getMessage());
                return back()->with('error', 'Terjadi kesalahan sistem saat mencoba menjalankan build: ' . $e->getMessage());
            }
        }

        $warehouseStockModel = '\\Modules\\PrSystem\\Models\\WarehouseStock';
        $stockMovementModel = '\\Modules\\PrSystem\\Models\\StockMovement';
        $budgetModel = '\\Modules\\PrSystem\\Models\\Budget';

        if (! class_exists($warehouseStockModel) || ! class_exists($stockMovementModel) || ! class_exists($budgetModel)) {
            return back()->with('error', 'Fitur reset warehouse tidak tersedia karena modul PR tidak aktif.');
        }

        $warehouseStockModel::truncate();
        $stockMovementModel::truncate();
        $budgetModel::query()->update(['used_amount' => 0]);

        return back()->with('success', 'Data Warehouse, Movement, dan Budget Used Amount berhasil direset.');
    }

    public static function getStatus()
    {
        $file = base_path(self::FILE);
        $status = is_file($file) ? json_decode(file_get_contents($file), true) : null;

        if (! is_array($status)) {
            $status = [];
        }

        $status = array_merge(self::defaultStatus(), $status);

        foreach (self::MODULE_KEY_MAP as $alias => $storageKey) {
            $status[$alias] = $status[$storageKey] ?? $status[$alias] ?? true;
        }

        return $status;
    }

    private static function defaultStatus()
    {
        return [
            'ServiceAgreementSystem' => true,
            'QcComplaintSystem' => true,
            'SystemISPO' => true,
            'management' => true,
            'PrSystem' => true,
            'SystemSupport' => true,
            'LabSystem' => true,
        ];
    }

    private static function storageKey(string $module): string
    {
        return self::MODULE_KEY_MAP[$module] ?? $module;
    }

    private static function persistStatus(array $status): void
    {
        $file = base_path(self::FILE);
        $payload = [];

        foreach (self::defaultStatus() as $key => $defaultValue) {
            $payload[$key] = (bool) ($status[$key] ?? $defaultValue);
        }

        file_put_contents(
            $file,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );
    }
}
