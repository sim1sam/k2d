@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-orange-600 text-black-50">
                <h3 class="mb-0">View Cash In Transaction</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Date:</th>
                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td>{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Source:</th>
                        <td>{{ $transaction->source }}</td>
                    </tr>
                    <tr>
                        <th>Bank Name:</th>
                        <td>{{ $transaction->bank->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                    </tr>
                    <tr>
                        <th>Payment Mode:</th>
                        <td>{{ ucfirst($transaction->payment_mode) }}</td>
                    </tr>
                    <tr>
                        <th>Notes:</th>
                        <td>{{ $transaction->notes ?? 'N/A' }}</td>
                    </tr>
                </table>

                <a href="{{ route('backend.cashIn.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
@endsection
