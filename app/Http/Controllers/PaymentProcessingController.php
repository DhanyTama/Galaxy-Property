<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use App\Models\User;
use App\Notifications\SalaryPaymentCompleted;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentProcessingController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->role->name !== 'Finance') {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman pemrosesan pembayaran.');
        }

        $approvedRequests = SalaryRequest::where('status', 'approved')->with(['user', 'approvedBy'])->latest()->paginate(10);

        return view('payment-process.index', compact('approvedRequests'));
    }

    public function complete(Request $request, SalaryRequest $salaryRequest): RedirectResponse
    {
        if (auth()->user()->role->name !== 'Finance') {
            abort(403, 'Anda tidak memiliki akses untuk memproses pembayaran.');
        }

        if ($salaryRequest->status !== 'approved') {
            return back()->with('error', 'Permintaan ini sudah tidak dalam status disetujui atau sudah dibayar.');
        }

        $salaryRequest->update([
            'status' => 'paid',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $financeUser = $salaryRequest->user;
        if ($financeUser) {
            $financeUser->notify(new SalaryPaymentCompleted($salaryRequest));
        }
        $directors = User::whereHas('role', function ($query) {
            $query->where('name', 'Director');
        })->get();
        foreach ($directors as $director) {
            $director->notify(new SalaryPaymentCompleted($salaryRequest));
        }

        return redirect()->route('payment-process.index')->with('success', 'Pembayaran gaji berhasil diproses dan bukti diunggah!');
    }
}
