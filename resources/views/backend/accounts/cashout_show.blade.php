@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Cash Out Details</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th>Bank</th>
                        <td>{{ $cashOut->bank->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>{{ number_format($cashOut->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Recipient</th>
                        <td>{{ $cashOut->recipient }}</td>
                    </tr>
                    <tr>
                        <th>Payment Mode</th>
                        <td>{{ ucfirst($cashOut->payment_mode) }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $cashOut->category)) }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $cashOut->notes ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ \Carbon\Carbon::parse($cashOut->date)->format('d M, Y') }}</td>
                    </tr>
                    </tbody>
                </table>

                <div class="row justify-content-between">
                    <div class="col-md-6">
                        <a href="{{ route('backend.cashOutEdit', $cashOut->id) }}" class="btn btn-warning">
                            <i class="las la-edit"></i> Edit
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <form action="{{ route('backend.cashOutDelete', $cashOut->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="las la-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
