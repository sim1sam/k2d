@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <!-- Header -->
            <div class="card-header bg-orange text-white">
                <h4 class="m-0">Customers for Offer: {{ $offer->title }}</h4>
            </div>

            <!-- Body -->
            <div class="card-body">
                <div class="text-center">
                    <a href="{{ route('backend.offers.index') }}" class="btn btn-primary">Back to Offer List</a>
                </div>
                <!-- Display Success/Error Messages -->
                @if(session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                @elseif(session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif
                <!-- Customer List -->
                <div class="customer-list mt-3">
                    @foreach($offer->customers as $customer)
                        <div class="border rounded p-2 mb-2">
                            <span>{{ $customer->name }} ({{ ucfirst($customer->status) }})</span>
                            <form action="{{ route('offers.remove-customer') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <button type="submit" class="btn btn-danger btn-sm ml-2">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <!-- No Customers Message -->
                @if($offer->customers->isEmpty())
                    <p>No customers attached to this offer.</p>
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-orange {
            background-color: #E65100; /* Orange Header */
        }
    </style>
@endsection
