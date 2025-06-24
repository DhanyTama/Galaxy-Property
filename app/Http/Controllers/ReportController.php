<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function paidSalaries(): View
    {
        if (auth()->user()->role->name !== 'Director') {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan ini.');
        }

        $paidSalaries = SalaryRequest::where('status', 'paid')->with(['user', 'approvedBy', 'processedBy'])->latest()->paginate(15);

        return view('reports.paid_salaries', compact('paidSalaries'));
    }
}
