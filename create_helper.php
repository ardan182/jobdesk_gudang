<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::statement("CREATE TABLE IF NOT EXISTS task_terima_supplier_helpers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_terima_supplier_id BIGINT UNSIGNED NOT NULL,
    warehouse_employee_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY tth_uniq (task_terima_supplier_id, warehouse_employee_id),
    CONSTRAINT fk_tth_task FOREIGN KEY (task_terima_supplier_id) REFERENCES task_terima_suppliers(id) ON DELETE CASCADE,
    CONSTRAINT fk_tth_emp FOREIGN KEY (warehouse_employee_id) REFERENCES warehouse_employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

echo "Table task_terima_supplier_helpers created successfully.\n";
