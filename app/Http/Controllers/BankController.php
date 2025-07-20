<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class BankController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_banks'])->only('index');
        $this->middleware(['permission:add_bank'])->only('create');
        $this->middleware(['permission:edit_bank'])->only('edit');
        $this->middleware(['permission:delete_bank'])->only('destroy');
    
        $this->bank_rules = [
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'branch' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['required', 'numeric'], // Ensure it's numeric
            'current_balance' => ['required', 'numeric'], // Ensure it's numeric
        ];
    
        $this->bank_messages = [
            'name.required' => translate('Bank name is required'),
            'account_number.required' => translate('Account number is required'),
            'account_number.max' => translate('Account number must be within 50 characters'),
            'opening_balance.required' => translate('Opening balance is required'),
            'current_balance.required' => translate('Current balance is required'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $banks = Bank::query();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $banks = $banks->where('name', 'like', '%' . $sort_search . '%')
                           ->orWhere('account_number', 'like', '%' . $sort_search . '%');
        }

        $banks = $banks->orderBy('created_at', 'desc')->paginate(15);
        return view('backend.banks.index', compact('banks', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = $this->bank_rules;
        $messages = $this->bank_messages;
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return Redirect::back()->withErrors($validator);
        }
    
        $bank = new Bank();
        $bank->name = $request->name;
        $bank->account_number = $request->account_number;
        $bank->branch = $request->branch;
        $bank->country = $request->country;
        
        // Handle the opening_balance and current_balance
        $bank->opening_balance = $request->opening_balance;  // Assign the decimal value
        $bank->current_balance = $request->current_balance;  // Assign the decimal value
        
        $bank->save();
    
        flash(translate('Bank has been created successfully!'))->success();
        return redirect()->route('backend.banks.index');
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('backend.banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $rules = $this->bank_rules;
        $messages = $this->bank_messages;
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            flash(translate('Sorry! Something went wrong'))->error();
            return Redirect::back()->withErrors($validator);
        }
    
        // Update the bank details
        $bank->name = $request->name;
        $bank->account_number = $request->account_number;
        $bank->branch = $request->branch;
        $bank->country = $request->country;
        $bank->opening_balance = $request->opening_balance;
        $bank->current_balance = $request->current_balance;
        $bank->save();
    
        flash(translate('Bank has been updated successfully!'))->success();
    
        // Redirect to the show page for the updated bank
        return redirect()->route('backend.banks.show', $bank->id);
    }
    

        public function show($id)
    {
        $bank = Bank::findOrFail($id); // Find the bank by ID or fail if not found

        return view('backend.banks.show', compact('bank')); // Pass the bank to the show view
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        // Delete the bank record
        $bank->delete();
    
        // Flash a success message
        flash(translate('Bank has been deleted successfully!'))->success();
    
        // Redirect back to the bank list
        return back();
    }
    
}
