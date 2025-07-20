@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Add New Bank') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('backend.banks.store') }}" method="POST">
                        @csrf

                        <!-- Bank Name -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Bank Name') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Account Number') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}" required>
                                @error('account_number')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Branch Name') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="branch" class="form-control" value="{{ old('branch') }}" required>
                                @error('branch')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Opening Balance -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Opening Balance') }}</label>
                            <div class="col-md-9">
                                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance') }}" required>
                                @error('opening_balance')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Balance -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Current Balance') }}</label>
                            <div class="col-md-9">
                                <input type="number" step="0.01" name="current_balance" class="form-control" value="{{ old('current_balance') }}" required>
                                @error('current_balance')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Country') }}</label>
                            <div class="col-md-9">
                                <input type="text" name="country" class="form-control" value="{{ old('country') }}" required>
                                @error('country')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
