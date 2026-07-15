<?php

use App\Exports\SuppliersExport;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/suppliers/template', function () {
    return Excel::download(new SuppliersExport, 'template-supplier.xlsx');
})->name('suppliers.template.download');
