@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-orange-600 text-black-50">
                <h3 class="mb-0">Edit Cash In Transaction</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.cashIn.update', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $transaction->date }}" required>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" class="form-control" value="{{ $transaction->amount }}" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="source">Source</label>
                        <input type="text" name="source" class="form-control" value="{{ $transaction->source }}" required>
                    </div>

                    <div class="form-group">
                        <label for="bank_id">Bank</label>
                        <select name="bank_id" class="form-control">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ $transaction->bank_id == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ $transaction->category }}">
                    </div>

                    <div class="form-group">
                        <label for="payment_mode">Payment Mode</label>
                        <input type="text" name="payment_mode" class="form-control" value="{{ $transaction->payment_mode }}">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" class="form-control">{{ $transaction->notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('backend.cashIn.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
