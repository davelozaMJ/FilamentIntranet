<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Http\Request;

class ExportPDF extends Controller
{
    public function TimesheetRecords (User $user) {
        $pdf = Pdf::loadView('welcome');
        return $pdf->download('invoice.pdf'); 
    }
}
