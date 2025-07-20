@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Edit Offer Information') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('backend.offers.update', $offer->id) }}" method="POST" enctype="multipart/form-data">
                @method('PATCH')
                @csrf

                <!-- Offer Title -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Offer Title') }}</label>
                        <div class="col-md-9">
                            <input type="text" name="title" class="form-control" value="{{ $offer->title }}" required>
                            @error('title')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Offer Description -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Description') }}</label>
                        <div class="col-md-9">
                            <textarea name="description" class="form-control" rows="4">{{ $offer->description }}</textarea>
                            @error('description')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Percentage -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Discount (%)') }}</label>
                        <div class="col-md-9">
                            <input type="number" step="0.01" name="discount" class="form-control" value="{{ $offer->discount }}" required>
                            @error('discount')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Start Date') }}</label>
                        <div class="col-md-9">
                            <input type="date" name="start_date" class="form-control" value="{{ $offer->start_date }}" required>
                            @error('start_date')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('End Date') }}</label>
                        <div class="col-md-9">
                            <input type="date" name="end_date" class="form-control" value="{{ $offer->end_date }}" required>
                            @error('end_date')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Offer Image -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Offer Image') }}</label>
                        <div class="col-md-9">
                            <input type="file" name="image" class="form-control">
                            @if ($offer->image)
                                <div class="mt-2">
                                    <img src="{{ asset('public/' . $offer->image) }}" alt="Offer Image" width="150">
                                </div>
                            @endif
                            @error('image')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Offer Status (Newly Added) -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Offer Status') }}</label>
                        <div class="col-md-9">
                            <select name="status" class="form-control" required>
                                <option value="active" {{ $offer->status == 'active' ? 'selected' : '' }}>{{ translate('Active') }}</option>
                                <option value="inactive" {{ $offer->status == 'inactive' ? 'selected' : '' }}>{{ translate('Inactive') }}</option>
                            </select>
                            @error('status')
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
@endsection
