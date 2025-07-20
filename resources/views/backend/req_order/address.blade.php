@extends('backend.layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-custom-orange text-white">
                <h5 class="mb-0">Edit Order - {{ $order->order_no }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.req.orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Address -->
                    <div class="form-group">
                        <label class="font-weight-bold">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $order->address) }}" placeholder="Enter Address">
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="font-weight-bold">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $order->phone) }}" placeholder="Enter Phone Number">
                    </div>

                    <!-- State -->
                    <div class="form-group">
                        <label class="font-weight-bold">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $order->state) }}" placeholder="Enter State">
                    </div>

                    <!-- City -->
                    <div class="form-group">
                        <label class="font-weight-bold">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $order->city) }}" placeholder="Enter City">
                    </div>

                    <!-- Postal Code -->
                    <div class="form-group">
                        <label class="font-weight-bold">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $order->postal_code) }}" placeholder="Enter Postal Code">
                    </div>

                    <!-- Country -->
                    <div class="form-group">
                        <label class="font-weight-bold">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $order->country) }}" placeholder="Enter Country" >
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-custom-orange">Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .bg-custom-orange {
            background-color: #FF5722 !important;
        }
        .btn-custom-orange {
            background-color: #FF5722 !important;
            color: white !important;
            border: none;
        }
        .btn-custom-orange:hover {
            background-color: #E64A19 !important;
        }
    </style>
@endsection
