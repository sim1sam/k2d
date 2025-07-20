@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Offer Details') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <!-- Offer Title -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><strong>{{ translate('Offer Title') }}:</strong></label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $offer->title }}</p>
                    </div>
                </div>

                <!-- Offer Description -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><strong>{{ translate('Description') }}:</strong></label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $offer->description }}</p>
                    </div>
                </div>

                <!-- Discount -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><strong>{{ translate('Discount (%)') }}:</strong></label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ number_format($offer->discount, 2) }}%</p>
                    </div>
                </div>

                <!-- Start Date -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><strong>{{ translate('Start Date') }}:</strong></label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $offer->start_date }}</p>
                    </div>
                </div>

                <!-- End Date -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><strong>{{ translate('End Date') }}:</strong></label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $offer->end_date }}</p>
                    </div>
                </div>

                <!-- Country -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><strong>{{ translate('Country') }}:</strong></label>
                    <div class="col-md-9">
                        <p class="form-control-plaintext">{{ $offer->country ?? translate('Not specified') }}</p>
                    </div>
                </div>

                <!-- Back & Edit Buttons -->
                <div class="form-group mb-0 text-right">
                    <a href="{{ route('backend.offers.index') }}" class="btn btn-secondary">{{ translate('Back') }}</a>
                    @can('edit_offer')
                        <a href="{{ route('backend.offers.edit', $offer->id) }}" class="btn btn-primary">{{ translate('Edit Offer') }}</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
