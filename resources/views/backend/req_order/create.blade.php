@extends('backend.layouts.app')

@section('content')
    <div class="container mt-4 p-4">
        <div class="card shadow-lg">
            <h5 class="card-header" style="background-color: #ff5722; color: white;">Create Order for Customer</h5>

            <form action="{{ route('backend.req.order.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf

            <!-- Customer Select (Searchable Dropdown) -->
                <div class="form-group">
                    <label for="customer_id" class="font-weight-bold text-muted">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control select2">
                        <option value="" disabled selected>Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Items -->
                <div id="orderItems">
                    <div class="order-item card p-3 mb-3 border">
                        <p class="font-weight-bold text-warning">Item 1</p>
                        <div class="form-row">
                            <div class="form-group col-md-12 col-sm-12">
                                <input type="text" class="form-control" name="items[0][product_name]" placeholder="Product Name">
                            </div>
                            <div class="form-group col-md-12 col-sm-12">
                                <input type="url" class="form-control" name="items[0][product_link]" placeholder="Product Link">
                            </div>
                            <div class="form-group col-md-12 col-sm-12">
                                <input type="text" class="form-control" name="items[0][size]" placeholder="Size">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <input type="number" class="form-control" name="items[0][quantity]" placeholder="Quantity">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <input type="number" class="form-control" name="items[0][price_bdt]" placeholder="Price (BDT)">
                            </div>
                            <div class="form-group col-md-12 col-sm-12">
                                <input type="text" class="form-control" name="items[0][note]" placeholder="Note">
                            </div>

                            <div class="form-group col-md-12 col-sm-12">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="fileUpload0" name="items[0][files][]" multiple onchange="displayFileNames(this)">
                                    <label class="custom-file-label" for="fileUpload0">Add Image (single/multiple)</label>
                                    <small id="fileNames0" class="form-text text-muted"></small>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-danger btn-sm deleteItem mt-3">Remove Item</button>
                    </div>
                </div>

                <!-- Add Item and Submit Button -->
                <div class="d-flex justify-content-between flex-wrap">
                    <button type="button" id="addItem" class="btn" style="background-color: #ff5722; color: white;">Add Item</button>
                    <button type="submit" class="btn" style="background-color: #ff5722; color: white;">Create Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function displayFileNames(input) {
            const fileNamesDiv = document.getElementById(`fileNames${input.id.replace('fileUpload', '')}`);
            const files = Array.from(input.files).map(file => file.name).join(', ');
            fileNamesDiv.textContent = files ? `Selected: ${files}` : '';
        }

        document.getElementById('addItem').addEventListener('click', function() {
            const orderItems = document.getElementById('orderItems');
            const itemCount = orderItems.getElementsByClassName('order-item').length;

            const newItem = `
                <div class="order-item card p-3 mb-3 border">
                    <p class="font-weight-bold text-warning">Item ${itemCount + 1}</p>
                    <div class="form-row">
                        <div class="form-group col-md-12 col-sm-12">
                            <input type="text" class="form-control" name="items[${itemCount}][product_name]" placeholder="Product Name">
                        </div>
                        <div class="form-group col-md-12 col-sm-12">
                            <input type="url" class="form-control" name="items[${itemCount}][product_link]" placeholder="Product Link">
                        </div>
                        <div class="form-group col-md-12 col-sm-12">
                            <input type="text" class="form-control" name="items[${itemCount}][size]" placeholder="Size">
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <input type="number" class="form-control" name="items[${itemCount}][quantity]" placeholder="Quantity">
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <input type="number" class="form-control" name="items[${itemCount}][price_bdt]" placeholder="Price (BDT)">
                        </div>
                        <div class="form-group col-md-12 col-sm-12">
                            <input type="text" class="form-control" name="items[${itemCount}][note]" placeholder="Note">
                        </div>

                        <div class="form-group col-md-12 col-sm-12">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="fileUpload${itemCount}" name="items[${itemCount}][files][]" multiple onchange="displayFileNames(this)">
                                <label class="custom-file-label" for="fileUpload${itemCount}">Add Image (single/multiple)</label>
                                <small id="fileNames${itemCount}" class="form-text text-muted"></small>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm deleteItem mt-3">Remove Item</button>
                </div>`;

            orderItems.insertAdjacentHTML('beforeend', newItem);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('deleteItem')) {
                const item = e.target.closest('.order-item');
                item.remove();
            }
        });

        // Initialize the select2 for customer search
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
