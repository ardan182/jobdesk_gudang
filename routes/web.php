<?php

use App\Exports\SupplierTemplateExport;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/suppliers/template', function () {
    return app(SupplierTemplateExport::class)->download();
})->name('suppliers.template.download');
