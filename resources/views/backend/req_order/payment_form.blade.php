@extends('backend.layouts.app')

@section('content')
    <div class="card shadow-sm border rounded-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-18 fw-600">{{ translate('Make Payment') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('order.payment') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <div class="form-group">
                    <label for="amount">{{ translate('Amount') }}</label>
                    <input type="number" name="amount" id="amount" class="form-control" value="{{ $item->due }}" required>
                </div>
                <div class="form-group">
                    <label for="payment_method">{{ translate('Select Payment Method') }}</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="">{{ translate('Choose Bank or SSL') }}</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                        <option value="ssl">{{ translate('SSL Payment Gateway') }}</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">{{ translate('Confirm Payment') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
