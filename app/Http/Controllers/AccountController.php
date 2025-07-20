<?php

namespace App\Http\Controllers;


use App\Models\BankTransaction;
use App\Models\CashIn;
use App\Models\CashOut;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class AccountController extends Controller
{
    public function showCashInForm()
    {
        $banks = Bank::all(); // Fetch all banks
        return view('backend.accounts.cashincreate', compact('banks'));
    }
    public function CashInindex(Request $request)
    {
        $query = CashIn::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('id', $search)
                ->orWhere('source', 'LIKE', "%{$search}%")
                ->orWhereHas('bank', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
        }

        $transactions = $query->latest()->paginate(10);

        return view('backend.accounts.cashinindex', compact('transactions'));
    }
    public function CashIn(Request $request)
    {
        $data = $request->validate([
            'bank_id' => 'nullable|exists:banks,id', // Nullable, as cash-in may not involve a bank
            'amount' => 'required|numeric|min:0',
            'source' => 'required|string|max:255',
            'payment_mode' => 'required|string',
            'category' => 'required|string',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $amount = (float) $data['amount'];

                // If a bank is selected, increment the bank's balance and record the transaction
                if (!empty($data['bank_id'])) {
                    $bank = Bank::findOrFail($data['bank_id']);
                    $bank->increment('current_balance', $amount); // Increment bank balance for cash-in

                    // Log the transaction in BankTransaction
                    try {
                        BankTransaction::create([
                            'bank_id' => $data['bank_id'],
                            'amount' => $amount,
                            'transaction_type' => 'cash_in', // Type of transaction is cash_in
                            'source' => $data['source'], // Source of cash-in (e.g., customer, etc.)
                            'category' => $data['category'],
                            'payment_mode' => $data['payment_mode'],
                            'notes' => $data['notes'],
                            'date' => $data['date'],
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('BankTransaction Error: ' . $e->getMessage());
                        throw $e; // Re-throw the exception to rollback the transaction
                    }
                }

                // Save the cash-in record
                CashIn::create($data);
            });

            return redirect()->route('backend.cashIn.index')->with('success', 'Cash In recorded successfully!');
        } catch (\Exception $e) {
            \Log::error('Cash In Error: ' . $e->getMessage());
            return redirect()->route('backend.cashIn.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $transaction = CashIn::findOrFail($id);
        return view('backend.accounts.cashinshow', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = CashIn::findOrFail($id);
        $banks = Bank::all(); // Fetch all banks for selection

        return view('backend.accounts.cashinedit', compact('transaction', 'banks'));
    }
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'source' => 'required|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'category' => 'nullable|string|max:255',
            'payment_mode' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($data, $id) {
                $transaction = CashIn::findOrFail($id);
                $oldAmount = $transaction->amount;
                $newAmount = (float) $data['amount'];

                // Update bank balance if bank_id is provided
                if (!empty($data['bank_id'])) {
                    $bank = Bank::findOrFail($data['bank_id']);

                    // Adjust bank balance based on the difference
                    $difference = $newAmount - $oldAmount;
                    $bank->increment('current_balance', $difference);
                }

                // Update the transaction
                $transaction->update($data);
            });

            return redirect()->route('backend.cashIn.index')->with('success', 'Cash In updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('backend.cashIn.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaction = CashIn::findOrFail($id);

                // Update bank balance if bank_id is associated
                if (!empty($transaction->bank_id)) {
                    $bank = Bank::findOrFail($transaction->bank_id);
                    $bank->decrement('current_balance', $transaction->amount);
                }

                // Delete transaction
                $transaction->delete();
            });

            return redirect()->route('backend.cashIn.index')->with('success', 'Cash In deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('backend.cashIn.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    //cashout
    public function createCashOut()
    {
        $banks = Bank::all(); // Fetch all banks for selection
        return view('backend.accounts.cashout_create', compact('banks'));
    }



    public function storeCashOut(Request $request)
    {
        $data = $request->validate([
            'bank_id' => 'nullable|exists:banks,id', // Nullable, as cash-out may not involve a bank
            'amount' => 'required|numeric|min:0',
            'recipient' => 'required|string|max:255',
            'payment_mode' => 'required|string',
            'category' => 'required|string',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $amount = (float) $data['amount'];

                // If a bank is selected, deduct from its balance and record the transaction
                if (!empty($data['bank_id'])) {
                    $bank = Bank::findOrFail($data['bank_id']);
                    $bank->decrement('current_balance', $amount);

                    // Log the transaction in BankTransaction
                    BankTransaction::create([
                        'bank_id' => $data['bank_id'],
                        'amount' => $amount,
                        'transaction_type' => 'cash_out',
                        'source' => $data['recipient'], // Recipient as the source of cash-out
                        'category' => $data['category'],
                        'payment_mode' => $data['payment_mode'],
                        'notes' => $data['notes'],
                        'date' => $data['date'],
                    ]);
                }

                // Save the cash-out record
                CashOut::create($data);
            });

            return redirect()->route('backend.cashOut.index')->with('success', 'Cash Out recorded successfully!');
        } catch (\Exception $e) {
            \Log::error('Cash Out Error: ' . $e->getMessage());
            return redirect()->route('backend.cashOut.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    // Show all Cash Out transactions
    public function indexCashOut(Request $request)
    {
        $query = CashOut::with('bank')->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('bank', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            })
                ->orWhere('recipient', 'LIKE', "%$search%")
                ->orWhere('date', 'LIKE', "%$search%");
        }

        $transactions = $query->get();
        return view('backend.accounts.cashout_index', compact('transactions'));
    }

// In AccountController.php

    public function editCashOut($id)
    {
        // Find the CashOut transaction by ID
        $cashOut = CashOut::findOrFail($id);

        // Get the list of banks to populate the bank selection dropdown
        $banks = Bank::all();

        // Return the edit view with the CashOut data and available banks
        return view('backend.accounts.cashout_edit', compact('cashOut', 'banks'));
    }

    public function updateCashOut(Request $request, $id)
    {
        $cashOut = CashOut::findOrFail($id);

        $data = $request->validate([
            'bank_id' => 'nullable|exists:banks,id',
            'amount' => 'required|numeric|min:0',
            'recipient' => 'required|string|max:255',
            'payment_mode' => 'required|string',
            'category' => 'required|string',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        DB::transaction(function () use ($data, $cashOut) {
            // If bank_id is provided, update the bank balance
            if (!empty($data['bank_id'])) {
                $bank = Bank::findOrFail($data['bank_id']);
                $bank->decrement('current_balance', $data['amount']);
            }

            // Update the CashOut transaction
            $cashOut->update($data);
        });

        return redirect()->route('backend.cashOut.index')->with('success', 'Cash Out updated successfully!');
    }
    public function showCashOut($id)
    {
        $cashOut = CashOut::findOrFail($id);
        return view('backend.accounts.cashout_show', compact('cashOut'));
    }
    public function deleteCashOut($id)
    {
        $cashOut = CashOut::findOrFail($id);
        $cashOut->delete();

        return redirect()->route('backend.cashOut.index')->with('success', 'Cash Out deleted successfully!');
    }

    public function Transindex($id = null)
    {
        // Get all BankTransaction records
        $bankTransactions = BankTransaction::orderBy('created_at', 'desc')->get();

        // If $id is provided, filter transactions by bank_id
        if ($id) {
            $bankTransactions = $bankTransactions->where('bank_id', $id);
            $selectedBank = Bank::find($id);
        } else {
            // If no bank ID is provided, show all transactions
            $selectedBank = null;
        }

        // Group the transactions by bank_id
        $groupedTransactions = $bankTransactions->groupBy('bank_id');

        // Get the bank details for each group
        $banks = Bank::whereIn('id', $groupedTransactions->keys())->get()->keyBy('id');

        return view('backend.accounts.transaction', compact('groupedTransactions', 'banks', 'selectedBank'));
    }


    public function downloadCashInInvoice($id)
    {
        // Fetch the cash-in transaction details
        $transaction = CashIn::findOrFail($id);

        // Load the invoice Blade view and pass cash-in data
        $pdf = PDF::loadView('backend.accounts.cashin_invoice', compact('transaction'));

        // Return the PDF for download
        return $pdf->download('cashin_invoice' . $transaction->id . '.pdf');
    }
    public function downloadCashOutInvoice($id)
    {
        // Fetch the cash-in transaction details
        $transaction = CashOut::findOrFail($id);

        // Load the invoice Blade view and pass cash-in data
        $pdf = PDF::loadView('backend.accounts.cashout_invoice', compact('transaction'));

        // Return the PDF for download
        return $pdf->download('cashout_invoice' . $transaction->id . '.pdf');
    }

}
