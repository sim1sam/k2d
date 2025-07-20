@extends('backend.layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-custom-orange text-white">
                <h5 class="mb-0">Edit Order for Customer - {{ $order->order_no }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.req.order.update', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Customer Name -->
                    <div class="form-group">
                        <label class="font-weight-bold">Customer Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $order->customer->name }}" readonly>
                    </div>

                    <!-- Delivery Charge -->
                    <div class="form-group">
                        <label class="font-weight-bold">Delivery Charge (BDT)</label>
                        <input type="number" name="delivery_charge" value="{{ old('delivery_charge', $order->delivery_charge) }}" class="form-control" placeholder="Enter Delivery Charge" step="0.01" min="0">
                    </div>

                    <!-- Order Items -->
                    <div id="orderItems">
                        @foreach ($order->items as $index => $item)
                            <div class="order-item border p-3 mb-3 rounded">
                                <h6 class="text-custom-orange font-weight-bold">Item {{ $index + 1 }}</h6>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ old('items.' . $index . '.id', $item->id) }}">
                                <div class="form-group">
                                    <label class="font-weight-bold">Product Name</label>
                                    <input type="text" name="items[{{ $index }}][product_name]" value="{{ old('items.' . $index . '.product_name', $item->product_name) }}" class="form-control" placeholder="Product Name">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Product Link</label>
                                    <input type="url" name="items[{{ $index }}][product_link]" value="{{ old('items.' . $index . '.product_link', $item->product_link) }}" class="form-control" placeholder="Product Link">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Size</label>
                                    <input type="text" name="items[{{ $index }}][size]" value="{{ old('items.' . $index . '.size', $item->size) }}" class="form-control" placeholder="Size">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Quantity</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" class="form-control" placeholder="Quantity">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Price (BDT)</label>
                                    <input type="number" name="items[{{ $index }}][price_bdt]" value="{{ old('items.' . $index . '.price_bdt', $item->price_bdt) }}" class="form-control" placeholder="Price (BDT)" step="0.01" min="0">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Note</label>
                                    <input type="text" name="items[{{ $index }}][note]" value="{{ old('items.' . $index . '.note', $item->note) }}" class="form-control" placeholder="Note">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Purchase Price (INR)</label>
                                    <input type="number" name="items[{{ $index }}][purchase_amount]" value="{{ old('items.' . $index . '.purchase_amount', $item->purchase_amount) }}" class="form-control" placeholder="Purchase Price (BDT)">
                                </div>
                                <!-- Purchase Bank Selection -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Select Purchase Bank</label>
                                    <select name="items[{{ $index }}][bank_id]" class="form-control">
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" @if (old('items.' . $index . '.bank_id', $item->bank_id ?? '') == $bank->id) selected @endif>
                                                {{ $bank->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Vendor Selection -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Vendor</label>
                                    <select name="items[{{ $index }}][vendor_id]" class="form-control">
                                        <option value="">Select Vendor</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" @if (old('items.' . $index . '.vendor_id', $item->vendor_id ?? '') == $vendor->id) selected @endif>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Brand Selection -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Brand</label>
                                    <select name="items[{{ $index }}][brand_id]" class="form-control">
                                        <option value="">Select Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}" @if (old('items.' . $index . '.brand_id', $item->brand_id ?? '') == $brand->id) selected @endif>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Shipment Selection -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Shipment</label>
                                    <select name="items[{{ $index }}][shipment_id]" class="form-control">
                                        <option value="">Select Shipment</option>
                                        @foreach ($shipments as $shipment)
                                            <option value="{{ $shipment->id }}" @if (old('items.' . $index . '.shipment_id', $item->shipment_id ?? '') == $shipment->id) selected @endif>
                                                {{ $shipment->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Image Upload -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Upload Image</label>
                                    <input type="file" name="items[{{ $index }}][image][]" class="form-control-file" multiple>
                                </div>

                                <div>
                                    @forelse ($item->files as $file)

                                        <a href="{{ asset('public/k2d/uploads/reqorder/' . basename($file->file_path)) }}" target="_blank">      <img src="{{ asset('public/k2d/uploads/reqorder/' . basename($file->file_path)) }}" alt="{{ $item->product_name }}" class="img-thumbnail" width="40">

                                        </a>

                                    @empty
                                        <img src="{{ asset('public/assets/img/placeholder.jpg') }}" class="img-thumbnail" width="80" alt="No image available">
                                    @endforelse
                                </div>




                                <!-- Remove Item Button -->
                                <button type="button" class="btn btn-danger removeItem">Remove Item</button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" id="addItem" class="btn btn-custom-orange">Add Item</button>
                        <button type="submit" class="btn btn-custom-orange">Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for Adding New Items -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = {{ count($order->items) }};

            document.getElementById('addItem').addEventListener('click', function () {
                let newItem = `
                    <div class="order-item border p-3 mb-3 rounded">
                        <h6 class="text-custom-orange font-weight-bold">Item ${itemIndex + 1}</h6>

                        <div class="form-group">
                            <label class="font-weight-bold">Product Name</label>
                            <input type="text" name="items[${itemIndex}][product_name]" class="form-control" placeholder="Product Name">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Product Link</label>
                            <input type="url" name="items[${itemIndex}][product_link]" class="form-control" placeholder="Product Link">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Size</label>
                            <input type="text" name="items[${itemIndex}][size]" class="form-control" placeholder="Size">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Quantity</label>
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Quantity">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Price (BDT)</label>
                            <input type="number" name="items[${itemIndex}][price_bdt]" class="form-control" placeholder="Price (BDT)" step="0.01" min="0">
                        </div>
                        <div class="form-group">
                                    <label class="font-weight-bold">Note</label>
                                    <input type="text" name="items[{{ $index }}][note]" value="{{ old('items.' . $index . '.note', $item->note) }}" class="form-control" placeholder="Note">
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Purchase Price (INR)</label>
                                    <input type="number" name="items[{{ $index }}][purchase_amount]" value="{{ old('items.' . $index . '.purchase_amount', $item->purchase_amount) }}" class="form-control" placeholder="Purchase Price (BDT)">
                         </div>

                         <!-- Purchase Bank Selection -->
                            <div class="form-group">
                            <label class="font-weight-bold">Select Purchase Bank</label>
                            <select name="items[{{ $index }}][bank_id]" class="form-control">
                            <option value="">Select Bank</option>
                            @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" @if (old('items.' . $index . '.bank_id', $item->bank_id ?? '') == $bank->id) selected @endif>
                            {{ $bank->name }}
                            </option>
                            @endforeach
                            </select>
                            </div>
                            <!-- Vendor Selection -->
                        <div class="form-group">
                        <label class="font-weight-bold">Vendor</label>
                        <select name="items[{{ $index }}][vendor_id]" class="form-control">
                        <option value="">Select Vendor</option>
                        @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @if (old('items.' . $index . '.vendor_id', $item->vendor_id ?? '') == $vendor->id) selected @endif>
                        {{ $vendor->name }}
                        </option>
                        @endforeach
                        </select>
                        </div>

                            <!-- Brand Selection -->
                        <div class="form-group">
                        <label class="font-weight-bold">Brand</label>
                        <select name="items[{{ $index }}][brand_id]" class="form-control">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @if (old('items.' . $index . '.brand_id', $item->brand_id ?? '') == $brand->id) selected @endif>
                        {{ $brand->name }}
                        </option>
                        @endforeach
                        </select>
                        </div>
                            <!-- Shipment Selection -->
                        <div class="form-group">
                        <label class="font-weight-bold">Shipment</label>
                        <select name="items[{{ $index }}][shipment_id]" class="form-control">
                        <option value="">Select Shipment</option>
                        @foreach ($shipments as $shipment)
                        <option value="{{ $shipment->id }}" @if (old('items.' . $index . '.shipment_id', $item->shipment_id ?? '') == $shipment->id) selected @endif>
                        {{ $shipment->name }}
                        </option>
                        @endforeach
                        </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Upload Image</label>
                            <input type="file" name="items[${itemIndex}][image][]" class="form-control-file" multiple>
                        </div>

                        <button type="button" class="btn btn-danger removeItem">Remove Item</button>
                    </div>
                `;

                document.getElementById('orderItems').insertAdjacentHTML('beforeend', newItem);
                itemIndex++;
            });

            document.getElementById('orderItems').addEventListener('click', function (event) {
                if (event.target.classList.contains('removeItem')) {
                    event.target.closest('.order-item').remove();
                }
            });
        });
    </script>

    <!-- Custom Styles -->
    <style>
        .bg-custom-orange {
            background-color: #FF5722 !important;
        }
        .text-custom-orange {
            color: #FF5722 !important;
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
