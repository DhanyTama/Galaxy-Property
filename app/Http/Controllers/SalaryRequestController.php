<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use App\Models\User;
use App\Notifications\SalaryRequest as NotificationsSalaryRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalaryRequestController extends Controller
{
    public function create(): View
    {
        if (auth()->user()->role->name !== 'Finance') {
            abort(403, 'Unauthorized action.');
        }

        return view('salary_requests.create');
    }

    private function calculatePPH($grossSalary)
    {
        if ($grossSalary <= 5000000) {
            return ['percentage' => 5, 'amount' => $grossSalary * 0.05];
        } elseif ($grossSalary > 5000000 && $grossSalary <= 20000000) {
            return ['percentage' => 10, 'amount' => $grossSalary * 0.10];
        } else {
            return ['percentage' => 15, 'amount' => $grossSalary * 0.15];
        }
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->role->name !== 'Finance') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'base_salary' => ['required', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
        ]);

        $baseSalary = (float) $validated['base_salary'];
        $bonus = (float) ($validated['bonus'] ?? 0);
        $grossSalary = $baseSalary + $bonus;

        $pphData = $this->calculatePPH($grossSalary);
        $pphAmount = $pphData['amount'];
        $pphPercentage = $pphData['percentage'];
        $netSalary = $grossSalary - $pphAmount;

        $salaryRequest = SalaryRequest::create([
            'user_id' => auth()->id(),
            'base_salary' => $baseSalary,
            'bonus' => $bonus,
            'pph_percentage' => $pphPercentage,
            'pph_amount' => $pphAmount,
            'net_salary' => $netSalary,
            'status' => 'pending',
        ]);


        $users = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['manager', 'director']);
        })->get();
        foreach ($users as $user) {
            $user->notify(new NotificationsSalaryRequest($salaryRequest));
        }

        return redirect()->route('salary-requests.create')->with('success', 'Permintaan pembayaran gaji berhasil diajukan!');
    }

    public function show(SalaryRequest $salaryRequest): View
    {
        if (in_array(auth()->user()->role->name, ['finance', 'director', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $salaryRequest->load(['user', 'approvedBy', 'processedBy']);

        return view('salary_requests.show', compact('salaryRequest'));
    }
}
