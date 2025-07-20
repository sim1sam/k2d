@extends('backend.layouts.app')

@section('content')
    <div class="container">
        <!-- Title & Balance -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Transactions</h2>
            @if($selectedBank)
                <h3 class="text-white px-4 py-2 rounded shadow text-center" style="background-color: #ff5722;">
                    Bank: {{ $selectedBank->name }} | Balance: {{ number_format($selectedBank->current_balance, 2) }}
                </h3>
            @else
                <h3 class="text-white px-4 py-2 rounded shadow text-center" style="background-color: #ff5722;">
                    Showing All Banks
                </h3>
            @endif
        </div>

        <!-- Transactions Table -->
        @foreach($banks as $bank)
            <h3 class="text-primary">{{ $bank->name }}</h3>

            @if(isset($groupedTransactions[$bank->id]))
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-center">
                                <thead class="text-white" style="background-color: #ff5722;">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Created At</th>
                                    <th>Amount</th>
                                    <th>Source/Recipient</th>
                                    <th>Category</th>
                                    <th>Payment Mode</th>
                                    <th>Notes</th>
                                    <th>Type</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($groupedTransactions[$bank->id] as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->date }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i A') }}</td>
                                        <td class="{{ $transaction->transaction_type == 'in' ? 'text-success font-weight-bold' : 'text-danger font-weight-bold' }}">
                                            {{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td>{{ $transaction->source}}</td>
                                        <td>{{ $transaction->category }}</td>
                                        <td>{{ $transaction->payment_mode }}</td>
                                        <td>{{ $transaction->notes }}</td>
                                        <td>
                                                <span class="{{ $transaction->transaction_type == 'cash_in' ? 'text-success' : 'text-danger' }}">
    {{ ucfirst($transaction->transaction_type) }}
</span>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
