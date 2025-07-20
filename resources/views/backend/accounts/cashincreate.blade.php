@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <!-- Form Card with Bootstrap 4 -->
        <div class="card shadow-lg">
            <div class="card-header bg-orange-600 text-black-50">
                <h3 class="mb-0">Cash In</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('backend.cashIn') }}" method="POST">
                @csrf

                <!-- Bank Selection -->
                    <div class="form-group">
                        <label for="bank_id">Select Bank</label>
                        <select name="bank_id" id="bank_id" class="form-control">
                            <option value="" disabled selected>Choose a bank</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" class="form-control" placeholder="Enter Amount" required>
                    </div>

                    <!-- Source -->
                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" name="source" id="source" class="form-control" placeholder="Enter Source">
                    </div>

                    <!-- Payment Mode -->
                    <div class="form-group">
                        <label for="payment_mode">Payment Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="" disabled selected>Select Payment Mode</option>
                            <option value="bank">Bank</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Partial</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" id="category" class="form-control">
                            <option value="" disabled selected>Select Category</option>
                            <option value="sales">Sales</option>
                            <option value="purchase">Purchase</option>
                            <option value="overhead_expense">Overhead Expense</option>
                            <option value="salary">Salary</option>
                            <option value="courier_charge">Courier Charge</option>
                            <option value="financial_expense">Financial Expense</option>
                            <option value="transportation">Transportation</option>
                            <option value="shipping_expense">Shipping Expense</option>
                            <option value="backpacker_expense">Backpacker Expense</option>
                            <option value="vat_tax">VAT/TAX</option>
                            <option value="entertainment">Entertainment</option>
                            <option value="personal_expense">Personal Expense</option>
                            <option value="vehicle_maintenance">Vehicle Maintenance</option>
                            <option value="fuel_expense">Fuel Expense</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes">Note</label>
                        <input type="text" name="notes" id="notes" class="form-control" placeholder="Enter Notes">
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" class="form-control">
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-orange btn-block">Cash In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
