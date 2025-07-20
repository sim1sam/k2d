@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <!-- Header -->
            <div class="card-header bg-orange text-black">
                <h4 class="m-0">Select Customers for Offers</h4>
            </div>

            <!-- Body -->
            <div class="card-body">
                <!-- Display success or error message -->
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @elseif(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <!-- Form to Select Customers -->
                <form action="{{ route('offers.attach-customers') }}" method="POST">
                @csrf
                <!-- Offer Selection -->
                    <div class="form-group">
                        <label for="offer">Select Offer:</label>
                        <select class="form-control" id="offer" name="offer_id" required>
                            <option selected disabled>Select an Offer</option>
                            @foreach($offers as $offer)
                                <option value="{{ $offer->id }}" {{ request('offer_id') == $offer->id ? 'selected' : '' }}>{{ $offer->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search and Filter Form -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="search">Search Customers:</label>
                            <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name or email">
                        </div>
                        <div class="col-md-4">
                            <label for="status">Filter by Status:</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">All</option>
                                <option value="newbie" {{ request('status') == 'newbie' ? 'selected' : '' }}>Newbie</option>
                                <option value="regular" {{ request('status') == 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="vip" {{ request('status') == 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>

                    <!-- Select All Checkbox -->
                    <div class="form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label" for="selectAll">Select All</label>
                    </div>

                    <!-- Customer List -->
                    <div class="customer-list mt-3">
                        @foreach($customers as $customer)
                            <div class="border rounded p-2 mb-2">
                                <input type="checkbox" class="form-check-input customer-checkbox" name="customer_ids[]" value="{{ $customer->id }}">
                                <label class="form-check-label">{{ $customer->name }} ({{ ucfirst($customer->status) }})</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success">Attach Customers</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <style>
        .bg-orange {
            background-color: #E65100; /* Orange Header */
        }
    </style>

    <script>
        // Select All Checkbox Functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.customer-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
@endsection
