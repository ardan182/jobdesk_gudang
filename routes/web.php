<?php

use App\Exports\EmployeesExport;
use App\Exports\SuppliersExport;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/suppliers/template', function () {
    return app(SuppliersExport::class)->download();
})->name('suppliers.template.download');

Route::get('/employees/template', function () {
    return app(EmployeesExport::class)->download();
})->name('employees.template.download');
