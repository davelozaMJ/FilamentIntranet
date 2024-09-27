<?php

use App\Http\Controllers\ExportPDF;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/personal');
});

Route::get('/PDFExport/{user}', function () {
    $pdf = Pdf::loadView('welcome');
    return $pdf->download('invoice.pdf');
})->name('exportPDF');

Route::get('pdf/generate/timesheet/{user}', [ExportPDF::class, 'TimesheetRecords'])->name('exportPDF2');
