@extends('backend.layouts.blank')

@section('content')
    <div class="h-100 d-flex align-items-center" style="min-height: 100vh;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <img src="{{ static_asset('assets/img/feedback.png') }}" alt="Feedback Image" class="img-fluid w-100" style="height: 100vh; object-fit: cover;">
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="p-5 w-100">
                        <div class="text-center mb-4">
                            <img src="{{ static_asset('assets/img/logo.png') }}" alt="Logo" class="mb-3" style="max-height: 60px;">
                            <h4>{{ translate('Feedback Form') }}</h4>
                        </div>
                        <form method="POST" action="{{ route('feedback.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="name">{{ translate('Full Name') }}</label>
                                <input id="name" type="text" class="form-control" name="name" required autofocus placeholder="{{ translate('Full Name') }}">
                            </div>
                            <div class="form-group">
                                <label for="mobile_number">{{ translate('Your Mobile Number (Used for Order)') }}</label>
                                <input id="mobile_number" type="text" class="form-control" name="mobile_number" required placeholder="{{ translate('Your Mobile Number (Used for Order)') }}">
                            </div>
                            <div class="form-group">
                                <label for="service_rating">{{ translate('Rate our service') }}</label>
                                <select id="service_rating" class="form-control" name="service_rating" required>
                                    <option value="">{{ translate('Select Rating') }}</option>
                                    <option value="1">{{ translate('Very Poor') }}</option>
                                    <option value="2">{{ translate('Poor') }}</option>
                                    <option value="3">{{ translate('Not Good Not Bad') }}</option>
                                    <option value="4">{{ translate('Satisfactory') }}</option>
                                    <option value="5">{{ translate('Impressive') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="suggestion">{{ translate('Any Suggestion') }}</label>
                                <textarea id="suggestion" class="form-control" name="suggestion" placeholder="{{ translate('Any Suggestion') }}"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="birthday">{{ translate('Your Birthday (DD-MMM)') }}</label>
                                <input id="birthday" type="date" class="form-control" name="birthday" placeholder="{{ translate('Your Birthday (DD-MMM)') }}">
                            </div>
                            <div class="form-group">
                                <label for="anniversary">{{ translate('Your Anniversary (DD-MMM)') }}</label>
                                <input id="anniversary" type="date" class="form-control" name="anniversary" placeholder="{{ translate('Your Anniversary (DD-MMM)') }}">
                            </div>
                            <input type="submit" class="btn btn-lg btn-block mt-3" style="background-color: #ff6c00; color: #fff;" value="{{ translate('Submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection