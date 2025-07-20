@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Add To Product Gallery') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Display Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                @endif

                <!-- Gallery Form -->
                    <form class="form-horizontal" action="{{ route('backend.gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Title') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                                @error('title')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Description') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                                @error('description')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Upload Images -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Upload Images') }}</label>
                            <div class="col-md-9">
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                <small class="form-text text-muted">{{ translate('You can upload multiple images.') }}</small>
                                @error('images')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save Gallery') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
