@extends('backend.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">Edit Cash Out</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.cashOutUpdate', $cashOut->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Bank Selection (Optional) -->
                            <div class="form-group">
                                <label for="bank_id">Select Bank (Optional)</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="" >Select a bank (Optional)</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ $cashOut->bank_id == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount -->
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" value="{{ $cashOut->amount }}" required>
                            </div>

                            <!-- Recipient -->
                            <div class="form-group">
                                <label for="recipient">Recipient</label>
                                <input type="text" name="recipient" id="recipient" class="form-control" value="{{ $cashOut->recipient }}" required>
                            </div>

                            <!-- Payment Mode -->
                            <div class="form-group">
                                <label for="payment_mode">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-control" required>
                                    <option value="" disabled>Select Payment Mode</option>
                                    <option value="bank" {{ $cashOut->payment_mode == 'bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="cash" {{ $cashOut->payment_mode == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit" {{ $cashOut->payment_mode == 'credit' ? 'selected' : '' }}>Credit</option>
                                    <option value="other" {{ $cashOut->payment_mode == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="" disabled>Select Category</option>
                                    <option value="purchase" {{ $cashOut->category == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                    <option value="salary" {{ $cashOut->category == 'salary' ? 'selected' : '' }}>Salary</option>
                                    <option value="expense" {{ $cashOut->category == 'expense' ? 'selected' : '' }}>General Expense</option>
                                    <option value="transportation" {{ $cashOut->category == 'transportation' ? 'selected' : '' }}>Transportation</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control">{{ $cashOut->notes }}</textarea>
                            </div>

                            <!-- Date -->
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ $cashOut->date }}" required>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-danger w-100">Update Cash Out</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
