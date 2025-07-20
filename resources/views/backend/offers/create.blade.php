@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Add New Offer') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('backend.offers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Offer Title -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Offer Title') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                                @error('title')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Offer Description -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Description') }}</label>
                            <div class="col-md-9">
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Discount Amount -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Discount') }} (%)</label>
                            <div class="col-md-9">
                                <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount') }}" required>
                                @error('discount')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Start Date') }}</label>
                            <div class="col-md-9">
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('End Date') }}</label>
                            <div class="col-md-9">
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Offer Image -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Offer Image') }}</label>
                            <div class="col-md-9">
                                <input type="file" name="image" class="form-control" required>
                                @error('image')
                                <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save Offer') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
