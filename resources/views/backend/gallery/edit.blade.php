@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Edit Gallery') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                        <div class="form-group">
                            <label>{{ translate('Title') }}</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $gallery->title) }}" required>
                            @error('title')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>{{ translate('Description') }}</label>
                            <textarea name="description" class="form-control">{{ old('description', $gallery->description) }}</textarea>
                            @error('description')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Existing Images -->
                        <div class="form-group">
                            <label>{{ translate('Current Images') }}</label>
                            <div class="d-flex flex-wrap">
                                @if($gallery->images && count(json_decode($gallery->images)) > 0)
                                    @foreach(json_decode($gallery->images) as $image)
                                        <div class="position-relative mr-2 mb-2">
                                            <img src="{{ url('public/gallery/' . $image) }}" class="img-thumbnail" width="100">
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ translate('No images available') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Upload New Images -->
                        <div class="form-group">
                            <label>{{ translate('Upload New Images') }}</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">{{ translate('You can upload multiple images') }}</small>
                            @error('images')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update Gallery') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
