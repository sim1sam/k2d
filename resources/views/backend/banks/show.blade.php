@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Bank Details') }}</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ translate('Bank Name') }}:</strong> {{ $bank->name }}</p>
                    <p><strong>{{ translate('Account Number') }}:</strong> {{ $bank->account_number }}</p>
                    <p><strong>{{ translate('Branch Name') }}:</strong> {{ $bank->branch }}</p>
                    <p><strong>{{ translate('Country Name') }}:</strong> {{ $bank->country }}</p>
                    <p><strong>{{ translate('Opening Balance') }}:</strong> {{ number_format($bank->opening_balance, 2) }}</p>
                    <p><strong>{{ translate('Current Balance') }}:</strong> {{ number_format($bank->current_balance, 2) }}</p>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('backend.banks.index') }}" class="btn btn-circle btn-info">{{ translate('Back to All Banks') }}</a>
            </div>
        </div>
    </div>
@endsection
