@extends('backend.layouts.app')

@section('content')
    <div class="container-fluid">  <!-- Make the container full width -->
        <div class="row justify-content-center">
            <div class="col-md-12"> <!-- Use the full-width column -->
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">Create Cash Out</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.cashOut') }}" method="POST">
                        @csrf

                        <!-- Bank Selection (Optional) -->
                            <div class="form-group">
                                <label for="bank_id">Select Bank (Optional)</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="" >Choose a bank (Optional)</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount -->
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount" required>
                            </div>

                            <!-- Recipient -->
                            <div class="form-group">
                                <label for="recipient">Recipient</label>
                                <input type="text" name="recipient" id="recipient" class="form-control" placeholder="Enter recipient" required>
                            </div>

                            <!-- Payment Mode -->
                            <div class="form-group">
                                <label for="payment_mode">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-control" required>
                                    <option value="" disabled selected>Select Payment Mode</option>
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="purchase">Purchase</option>
                                    <option value="salary">Salary</option>
                                    <option value="expense">General Expense</option>
                                    <option value="transportation">Transportation</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" placeholder="Enter additional notes"></textarea>
                            </div>

                            <!-- Date -->
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" id="date" class="form-control" required>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-danger w-100">Create Cash Out</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
