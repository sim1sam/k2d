@extends('frontend.layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <!-- Left Section -->
            <div class="col-lg-8 col-md-12">
                <!-- Checkout Form -->
                <form id="checkoutForm" action="{{ route('frontend.req.checkout.confirm') }}" method="POST">
                @csrf
                <!-- Shipping Info -->
                    <div class="card mb-3">
                        <div class="card-header text-white" style="background-color: #ff6c00;">
                            <strong>Shipping Info</strong>
                        </div>
                        <div class="card-body">
<!-- Default Address -->
<div class="shipping-info">
    @if ($defaultAddress)
        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Address:</strong>
            {{ $defaultAddress->address }},
            {{ $defaultAddress->city->name ?? '' }},
            {{ $defaultAddress->state->name ?? '' }},
            {{ $defaultAddress->country->name ?? '' }}
            - {{ $defaultAddress->postal_code }}
        </p>
        <p><strong>Phone:</strong> {{ $defaultAddress->phone }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>

        <!-- Show "Change" button only if a default address exists -->
        <div class="d-flex flex-wrap">
            <span class="btn btn-success btn-sm mr-2" id="changeAddressBtn" data-toggle="modal" data-target="#selectAddressModal">
                Change
            </span>
        </div>
    @else
        <p>No default address found. Please add a new address below.</p>

        <!-- Show "Add New Address" button only if no default address exists -->
        <span class="btn btn-dark btn-block fs-14 fw-500" onclick="add_new_address()" style="border-radius: 25px;">
            <i class="la la-plus fs-18 fw-700 mr-2"></i>
            {{ translate('Add New Address') }}
        </span>
    @endif
</div>

                            

                            <!-- Hidden Inputs for Address -->
                            <input type="hidden" name="address" id="selectedAddress" value="{{ $defaultAddress->address ?? '' }}">
                            <input type="hidden" name="country" id="selectedCountry" value="{{ $defaultAddress->country->name ?? '' }}">
                            <input type="hidden" name="state" id="selectedState" value="{{ $defaultAddress->state->name ?? '' }}">
                            <input type="hidden" name="city" id="selectedCity" value="{{ $defaultAddress->city->name ?? '' }}">
                            <input type="hidden" name="postal_code" id="selectedPostalCode" value="{{ $defaultAddress->postal_code ?? '' }}">
                            <input type="hidden" name="phone" id="selectedPhone" value="{{ $defaultAddress->phone ?? '' }}">
                            <input type="hidden" name="name" id="selectedName" value="{{ Auth::user()->name }}">
                            <input type="hidden" name="email" id="selectedEmail" value="{{ Auth::user()->email }}">
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="card mb-3">
                        <div class="form-check mt-3">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required checked readonly>
                            <label class="form-check-label" for="terms">
                                I agree to the terms and conditions
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lg w-100 text-white" style="background-color: #ff6c00;">Complete Order</button>
                </form>
                <!-- Checkout Form Ends Here -->
            </div>

            <!-- Right Section - Order Summary -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #ff6c00;">
                        <strong>Order Summary</strong>
                    </div>
                    <div class="card-body">
                        @php $total_price = 0; @endphp
                        @foreach ($order['items'] as $item)
                            @php
                                $item_total = $item['quantity'] * $item['price_bdt'];
                                $total_price += $item_total;
                            @endphp
                            <div class="d-flex justify-content-between mb-2">
                                <p class="mb-0"><strong>{{ $item['product_name'] }}</strong> ({{ $item['quantity'] }}x{{ $item['price_bdt'] }})</p>
                                <p class="mb-0">{{ $item_total }} BDT</p>
                            </div>
                        @endforeach
                        <h5 class="font-weight-bold mt-3"><strong>Total Price:</strong> {{ $total_price }} BDT</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Select Address Modal -->
    <div class="modal fade" id="selectAddressModal" tabindex="-1" role="dialog" aria-labelledby="selectAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #ff6c00;">
                    <h5 class="modal-title text-white">Select Delivery Address</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="address-list">
                        @foreach ($addresses as $address)
                            <div class="address-card card mb-2">
                                <div class="card-body">
                                    <p><strong>Address:</strong> {{ $address->address }},
                                        {{ $address->city->name ?? '' }},
                                        {{ $address->state->name ?? '' }},
                                        {{ $address->country->name ?? '' }} - {{ $address->postal_code }}</p>
                                    <button class="btn btn-primary btn-sm select-address-btn" data-id="{{ $address->id }}">Select as Delivery Address</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-dark btn-block fs-14 fw-500" onclick="add_new_address()" style="border-radius: 25px;">
                        <i class="la la-plus fs-18 fw-700 mr-2"></i>
                        {{ translate('Add New Address') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Handle Address Selection -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // When user selects an address
            $('.select-address-btn').on('click', function () {
                var addressId = $(this).data('id'); // Get selected address ID

                // Make an AJAX request to set the delivery address
                $.ajax({
                    url: "{{ route('frontend.req.checkout.updateAddress') }}", // Ensure this route is correct
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        address_id: addressId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Address selected successfully!');

                            // Update the hidden fields with the selected address data
                            $('#selectedAddress').val(response.address.address);
                            $('#selectedCity').val(response.address.city);
                            $('#selectedState').val(response.address.state);
                            $('#selectedCountry').val(response.address.country);
                            $('#selectedPostalCode').val(response.address.postal_code);
                            $('#selectedPhone').val(response.address.phone);
                            $('#selectedName').val(response.address.name);
                            $('#selectedEmail').val(response.address.email);

                            // Optionally, update the address display in the checkout form
                            $('.shipping-info').html(`
                                <p><strong>Name:</strong> ${response.address.name}</p>
                                <p><strong>Address:</strong> ${response.address.address},
                                    ${response.address.city}, ${response.address.state}, ${response.address.country} - ${response.address.postal_code}</p>
                                <p><strong>Phone:</strong> ${response.address.phone}</p>
                                <p><strong>Email:</strong> ${response.address.email}</p>
                            `);

                            // Close the modal
                            $('#selectAddressModal').modal('hide');
                        } else {
                            alert('Failed to select address. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        alert('An error occurred while selecting the address. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection



@section('modal')
    <!-- Wallet Recharge Modal -->
    @include('frontend.partials.wallet_modal')
    <script type="text/javascript">
        function show_wallet_modal() {
            $('#wallet_modal').modal('show');
        }
    </script>

    <!-- Address modal Modal -->
    @include('frontend.partials.address.address_modal')
@endsection


@section('script')
    @include('frontend.partials.address.address_js')

    @if (get_setting('google_map') == 1)
        @include('frontend.partials.google_map')
    @endif
@endsection
