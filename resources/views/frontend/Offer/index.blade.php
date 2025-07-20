@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card shadow-none rounded-0 border">
        <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-20 fw-700 text-dark">
                <i class="las la-gift text-primary"></i> {{ translate('Active Offers') }}
            </h5>

            <!-- Back Button -->
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="las la-arrow-left"></i> {{ translate('Back') }}
            </a>
        </div>

        <div class="card-body">
            <div class="row">
                @foreach ($offers as $offer)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card border shadow-sm h-100 position-relative overflow-hidden rounded-lg">
                            <!-- Offer Image -->
                            <div class="position-relative">
                                <img src="{{ asset('public/' . $offer->image) }}" class="card-img-top" alt="{{ $offer->title }}"
                                     style="height: 200px; object-fit: cover; transition: transform 0.3s ease-in-out;">
                            </div>

                            <!-- Status Badge (Top of Image) -->
                            <span class=" position-absolute"
                                  style="top: 10px; left: 10px; padding: 6px 12px; font-size: 14px; background: {{ $offer->status == 'active' ? '#28a745' : '#dc3545' }}; color: white;">
                                {{ translate(ucfirst($offer->status)) }}
                            </span>

                            <div class="card-body text-center">
                                <!-- Offer Title -->
                                <h5 class="card-title fs-16 fw-bold text-primary mb-2">{{ $offer->title }}</h5>

                                <!-- Discount -->
                                <p class="text-danger fw-bold fs-18 mb-3">
                                    <i class="las la-tag"></i> {{ $offer->discount }}% {{ translate('Off') }}
                                </p>

                                <!-- View Offer Button -->
                                <a href="{{ route('frontend.offer.show', $offer->id) }}" class="btn btn-primary btn-sm px-4">
                                    <i class="las la-eye"></i> {{ translate('View Offer') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $offers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
