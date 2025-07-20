@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="lg:mx-5 p-4">
        <div class="container mx-auto mt-4 p-4 px-5 md:p-6 rounded-lg shadow-lg bg-white">
            <h5 class="text-xl font-bold bg-warning text-white mb-6 p-3 rounded-lg">Create Order</h5>

            <form action="{{ route('frontend.order.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="customer_id" value="{{ auth()->user()->id }}">

                <div id="orderItems">
                    <div class="order-item p-4 border border-secondary rounded-lg bg-light shadow-sm mb-4">
                        <p class="text-lg font-semibold text-warning mb-2">Item 1</p>
                        <div class="form-group">
                            <input type="text" class="form-control mb-2" name="items[0][product_name]" placeholder="Product Name" required>
                            <input type="url" class="form-control mb-2" name="items[0][product_link]" placeholder="Product Link">
                            <input type="text" class="form-control mb-2" name="items[0][size]" placeholder="Size" required>
                            <input type="number" class="form-control mb-2" name="items[0][quantity]" placeholder="Quantity" required>
                            <input type="number" class="form-control mb-2" name="items[0][price_bdt]" placeholder="Price (BDT)" required>
                            <input type="text" class="form-control mb-2" name="items[0][note]" placeholder="Note">

                            <div class="custom-file mb-2">
                                <input type="file" id="fileUpload0" class="custom-file-input" name="items[0][files][]" multiple onchange="displayFileNames(this)" accept="image/jpeg,image/png,image/webp,image/jpg,application/pdf">
                                <label class="custom-file-label" for="fileUpload0">Add Image (single/multiple)</label>
                                <span id="fileNames0" class="text-muted text-sm"></span>
                            </div>
                            <div id="imagePreview0" class="mt-2 mb-3 d-flex flex-wrap"></div>
                        </div>

                        <button type="button" class="deleteItem btn btn-danger btn-sm">Remove Item</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button type="button" id="addItem" class="btn btn-warning btn-sm">Add Item</button>
                    <button type="submit" class="btn btn-warning btn-sm">Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function displayFileNames(input) {
            const fileNamesDiv = document.getElementById(`fileNames${input.id.replace('fileUpload', '')}`);
            const fileLabel = input.nextElementSibling;
            const previewDiv = document.getElementById(`imagePreview${input.id.replace('fileUpload', '')}`);
            const files = Array.from(input.files).map(file => file.name).join(', ');

            // Display selected file names next to the input
            fileNamesDiv.textContent = files ? `Selected: ${files}` : '';

            // Update the custom file label to show the file name
            if (files) {
                fileLabel.classList.add('selected');
                fileLabel.textContent = `${files}`;
            } else {
                fileLabel.classList.remove('selected');
                fileLabel.textContent = 'Add Image (single/multiple)';
            }
            
            // Clear previous previews
            previewDiv.innerHTML = '';
            
            // Generate previews for each file
            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach(file => {
                    const fileType = file.type.split('/')[0];
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    
                    if (fileType === 'image') {
                        // Create image preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imgContainer = document.createElement('div');
                            imgContainer.className = 'preview-item mr-2 mb-2';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-thumbnail';
                            img.style.maxHeight = '100px';
                            img.style.maxWidth = '100px';
                            img.title = file.name;
                            
                            imgContainer.appendChild(img);
                            previewDiv.appendChild(imgContainer);
                        };
                        reader.readAsDataURL(file);
                    } else if (fileExtension === 'pdf') {
                        // Create PDF icon
                        const pdfContainer = document.createElement('div');
                        pdfContainer.className = 'preview-item mr-2 mb-2 d-flex flex-column align-items-center';
                        
                        const pdfIcon = document.createElement('div');
                        pdfIcon.className = 'pdf-icon bg-light border rounded p-2 text-center';
                        pdfIcon.style.width = '60px';
                        pdfIcon.style.height = '60px';
                        pdfIcon.innerHTML = '<i class="las la-file-pdf text-danger" style="font-size: 2rem;"></i>';
                        
                        const pdfName = document.createElement('small');
                        pdfName.className = 'text-muted text-truncate';
                        pdfName.style.maxWidth = '80px';
                        pdfName.textContent = file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name;
                        pdfName.title = file.name;
                        
                        pdfContainer.appendChild(pdfIcon);
                        pdfContainer.appendChild(pdfName);
                        previewDiv.appendChild(pdfContainer);
                    }
                });
            }
        }

        document.getElementById('addItem').addEventListener('click', function() {
            const orderItems = document.getElementById('orderItems');
            const itemCount = orderItems.getElementsByClassName('order-item').length;

            const newItem = `
            <div class="order-item p-4 border border-secondary rounded-lg bg-light shadow-sm mb-4">
                <p class="text-lg font-semibold text-warning mb-2">Item ${itemCount + 1}</p>
                <div class="form-group">
                    <input type="text" class="form-control mb-2" name="items[${itemCount}][product_name]" placeholder="Product Name" required>
                    <input type="url" class="form-control mb-2" name="items[${itemCount}][product_link]" placeholder="Product Link">
                    <input type="text" class="form-control mb-2" name="items[${itemCount}][size]" placeholder="Size" required>
                    <input type="number" class="form-control mb-2" name="items[${itemCount}][quantity]" placeholder="Quantity" required>
                    <input type="number" class="form-control mb-2" name="items[${itemCount}][price_bdt]" placeholder="Price (BDT)" required>
                    <input type="text" class="form-control mb-2" name="items[${itemCount}][note]" placeholder="Note">

                    <div class="custom-file mb-2">
                        <input type="file" id="fileUpload${itemCount}" class="custom-file-input" name="items[${itemCount}][files][]" multiple onchange="displayFileNames(this)" accept="image/jpeg,image/png,image/webp,image/jpg,application/pdf">
                        <label class="custom-file-label" for="fileUpload${itemCount}">Add Image (single/multiple)</label>
                        <span id="fileNames${itemCount}" class="text-muted text-sm"></span>
                    </div>
                    <div id="imagePreview${itemCount}" class="mt-2 mb-3 d-flex flex-wrap"></div>
                </div>

                <button type="button" class="deleteItem btn btn-danger btn-sm">Remove Item</button>
            </div>`;

            orderItems.insertAdjacentHTML('beforeend', newItem);
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('deleteItem')) {
                event.target.closest('.order-item').remove();
            }
        });
    </script>
@endsection
