<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use App\Models\User;
use App\Notifications\SalaryRequestStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ApprovalController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->role->name !== 'Manager') {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman persetujuan.');
        }

        $pendingRequests = SalaryRequest::where('status', 'pending')->with('user')->latest()->paginate(10);

        return view('approvals.index', compact('pendingRequests'));
    }

    public function approve(Request $request, SalaryRequest $salaryRequest): RedirectResponse
    {
        if (auth()->user()->role->name !== 'Manager') {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui permintaan.');
        }

        if ($salaryRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah tidak dalam status pending.');
        }

        $salaryRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $financeUser = $salaryRequest->user;
        if ($financeUser) {
            $financeUser->notify(new SalaryRequestStatusUpdated($salaryRequest, 'approved'));
        }
        $directors = User::whereHas('role', function ($query) {
            $query->where('name', 'Director');
        })->get();
        foreach ($directors as $director) {
            $director->notify(new SalaryRequestStatusUpdated($salaryRequest, 'approved'));
        }

        return redirect()->route('approvals.index')->with('success', 'Permintaan gaji berhasil disetujui!');
    }

    public function reject(Request $request, SalaryRequest $salaryRequest): RedirectResponse
    {
        if (auth()->user()->role->name !== 'Manager') {
            abort(403, 'Anda tidak memiliki akses untuk menolak permintaan.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($salaryRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah tidak dalam status pending.');
        }

        $salaryRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $financeUser = $salaryRequest->user;
        if ($financeUser) {
            $financeUser->notify(new SalaryRequestStatusUpdated($salaryRequest, 'rejected'));
        }
        $directors = User::whereHas('role', function ($query) {
            $query->where('name', 'Director');
        })->get();
        foreach ($directors as $director) {
            $director->notify(new SalaryRequestStatusUpdated($salaryRequest, 'rejected'));
        }

        return redirect()->route('approvals.index')->with('success', 'Permintaan gaji berhasil ditolak!');
    }
}
