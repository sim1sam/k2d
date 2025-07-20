@extends('frontend.layouts.app')

@section('content')
    <!-- Gallery Section -->
    <div class="container mt-5 mb-5">
        <!-- Gallery Heading -->
        <div class="text-center mb-4">
            <h2 class="text-primary font-weight-bold">Our Gallery</h2>
            <p class="text-muted">Explore our amazing collection of images</p>
        </div>

        <!-- Gallery Grid -->
        <div class="row">
            @foreach ($galleries as $gallery)
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning">{{ $gallery->title }}</h5>
                            <p class="card-text text-secondary">{{ $gallery->description }}</p>

                            @if (!empty($gallery->images) && is_array($gallery->images))
                                <a href="#" data-gallery-id="{{ $loop->index }}" class="open-gallery">
                                   <img src="{{ url('public/gallery/' . $gallery->images[0]) }}""
                                         class="img-fluid rounded gallery-image"
                                         alt="{{ $gallery->title }}">
                                </a>

                                <!-- Hidden Gallery Images -->
                                <div class="d-none" id="gallery-{{ $loop->index }}">
                                    @foreach ($gallery->images as $image)
                                        <div class="image-item">
                                           <img src="{{ url('public/gallery/' . $image) }}" alt="{{ $gallery->title }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal for Full-Screen Lightbox -->
    <div class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close-btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Gallery Image">
                </div>
                <div class="modal-footer justify-content-between">
                    <button id="prevImage" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    <button id="nextImage" class="btn btn-secondary">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('frontend/assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

    <script>
        $(document).ready(function () {
            let currentGallery = [];
            let currentIndex = 0;

            $('.open-gallery').on('click', function (e) {
                e.preventDefault();
                $('#modalImage').attr('src', '');

                const galleryId = $(this).data('gallery-id');
                currentGallery = [];
                $(`#gallery-${galleryId} .image-item img`).each(function () {
                    currentGallery.push($(this).attr('src'));
                });

                currentIndex = 0;
                updateModalImage();
                $('#lightboxModal').modal('show');
            });

            function updateModalImage() {
                $('#modalImage').attr('src', currentGallery[currentIndex]);
            }

            $('#prevImage').on('click', function () {
                currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
                updateModalImage();
            });

            $('#nextImage').on('click', function () {
                currentIndex = (currentIndex + 1) % currentGallery.length;
                updateModalImage();
            });

            $('.close-btn').on('click', function () {
                $('#lightboxModal').modal('hide');
            });
        });
    </script>

    <!-- CSS -->
    <style>
        .gallery-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .gallery-image:hover {
            opacity: 0.8;
        }
        #modalImage {
            max-height: 80vh;
            object-fit: contain;
        }
    </style>
@endsection
