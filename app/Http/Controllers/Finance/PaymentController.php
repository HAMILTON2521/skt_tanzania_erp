<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\BankAccount;
use App\Models\Finance\Invoice;
use App\Models\Finance\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::query()
            ->with('invoice:id,invoice_number,customer_name,total_amount', 'bankAccount:id,account_name,bank_name,account_number')
            ->latest('payment_date')
            ->latest('id')
            ->get();

        $invoices = Invoice::query()
            ->orderBy('invoice_number')
            ->get(['id', 'invoice_number', 'customer_name', 'total_amount']);

        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('bank_name')
            ->get(['id', 'account_name', 'bank_name', 'account_number']);

        return view('admin.finance.payments', [
            'navigation' => config('admin.navigation', []),
            'payments' => $payments,
            'invoices' => $invoices,
            'bankAccounts' => $bankAccounts,
            'summary' => [
                'count' => $payments->count(),
                'completed' => $payments->where('status', 'completed')->count(),
                'pending' => $payments->where('status', 'pending')->count(),
                'total' => $payments->sum('amount'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_number' => ['required', 'string', 'max:50', 'unique:payments,payment_number'],
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'max:50'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:pending,completed,failed,reversed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Payment::query()->create($validated);

        return redirect()
            ->route('admin.finance.payments.index')
            ->with('status', 'Payment recorded successfully.');
    }
}
