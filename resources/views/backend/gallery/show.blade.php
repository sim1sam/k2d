@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Gallery Details') }}</h5>
                    <a href="{{ route('backend.gallery.index') }}" class="btn btn-secondary float-right">
                        {{ translate('Back to List') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Title -->
                    <h4 class="mb-3">{{ $gallery->title }}</h4>

                    <!-- Description -->
                    <p class="text-muted">{{ $gallery->description ?? translate('No description available') }}</p>

                    <!-- Images -->
                    <div class="row">
                        @if(!empty($gallery->images) && count(json_decode($gallery->images) ?? []) > 0)
                            @foreach(json_decode($gallery->images) as $index => $image)
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="card">
                                        <!-- Click Image to Open Modal -->
                                        <img src="{{ url('public/gallery/' . $image) }}" class="card-img-top img-fluid" alt="Gallery Image" style="height: 80px; cursor: pointer;" data-toggle="modal" data-target="#imageModal{{ $index }}">
                                    </div>
                                </div>

                                <!-- Bootstrap 4 Modal -->
                                <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel{{ $index }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body text-center">
                                                <img src="{{ url('public/gallery/' . $image) }}" class="img-fluid" alt="Gallery Image">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center w-100">{{ translate('No images available') }}</p>
                        @endif
                    </div>



                    <!-- Edit & Delete Buttons -->
                    <div class="mt-4">
                        <a href="{{ route('backend.gallery.edit', $gallery->id) }}" class="btn btn-warning">
                            {{ translate('Edit') }}
                        </a>
                        <form action="{{ route('backend.gallery.destroy', $gallery->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this gallery?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                {{ translate('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
