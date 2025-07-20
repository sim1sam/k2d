@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card shadow-sm border rounded-lg">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold"><i class="las la-tag"></i> {{ translate('Offer Details') }}</h5>

            <!-- Back Button -->
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="las la-arrow-left"></i> {{ translate('Back') }}
            </a>
        </div>

        <div class="card-body text-center">
            <!-- Offer Image -->
            <div class="position-relative">
                <img src="{{ asset('public/' . $offer->image) }}" class="img-fluid rounded shadow-sm mb-3"
                     style="max-height: 350px; width: 100%; object-fit: cover;">

                <!-- Status Badge (Top Right) -->
                <span class=" position-absolute"
                      style="top: 15px; right: 15px; padding: 8px 12px; font-size: 14px; background: {{ $offer->status == 'active' ? '#28a745' : '#dc3545' }}; color: white;">
                    {{ translate(ucfirst($offer->status)) }}
                </span>
            </div>

            <!-- Offer Title -->
            1	Sophia Dixon	No image available	Cumque repudiandae v	839            <h3 class="text-primary fw-bold mt-3">{{ $offer->title }}</h3>

            <!-- Discount -->
            <p class="text-danger fs-18 fw-bold">
                <i class="las la-percentage"></i> {{ $offer->discount }}% {{ translate('Off') }}
            </p>

            <!-- Offer Description -->
            <p class="text-secondary fs-16 px-3">{{ $offer->description }}</p>

            <!-- Start & End Date -->
            <p class="text-muted mt-2">
                <i class="las la-calendar"></i> {{ translate('Valid from') }}:
                <strong>{{ date('d M Y', strtotime($offer->start_date)) }}</strong> -
                <strong>{{ date('d M Y', strtotime($offer->end_date)) }}</strong>
            </p>
        </div>
    </div>
@endsection
