@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Cash Out Transactions</h3>
                <a href="{{ route('backend.cashOutCreate') }}" class="btn btn-light btn-sm">
                    <i class="las la-plus"></i> Create Cash Out
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Search Bar -->
                <form action="{{ route('backend.cashOut.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by Bank, Recipient, or Date..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-danger"><i class="las la-search"></i></button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                        <tr>
                            <th>In. No</th>
                            <th>Bank Name</th>
                            <th>Amount</th>
                            <th>Recipient</th>
                            <th>Payment Mode</th>
                            <th>Category</th>
                            <th>Notes</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $key => $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->bank->name ?? 'N/A' }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->recipient }}</td>
                                <td>{{ ucfirst($transaction->payment_mode) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                                <td>{{ $transaction->notes ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('backend.cashOutView', $transaction->id) }}" class="btn btn-sm btn-info p-1">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a href="{{ route('backend.cashOutEdit', $transaction->id) }}" class="btn btn-sm btn-warning p-1">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="{{ route('cashout.invoice.download', $transaction->id) }}" class="btn btn-sm btn-warning p-1">
                                    <i class="las la-file-invoice"></i>
                                    </a>
                                    <form action="{{ route('backend.cashOutDelete', $transaction->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger p-1">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
