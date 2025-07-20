@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Edit Bank Information') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('backend.banks.update', $bank->id) }}" method="POST">
                    @method('PATCH')
                    @csrf

                    <!-- Bank Name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Bank Name') }}</label>
                        <div class="col-md-9">
                            <input type="text" name="name" class="form-control" value="{{ $bank->name }}" required>
                            @error('name')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Account Number -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Account Number') }}</label>
                        <div class="col-md-9">
                            <input type="text" name="account_number" class="form-control" value="{{ $bank->account_number }}" required>
                            @error('account_number')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Branch Name -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Branch Name') }}</label>
                        <div class="col-md-9">
                            <input type="text" name="branch" class="form-control" value="{{ $bank->branch }}" required>
                            @error('branch')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Opening Balance -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Opening Balance') }}</label>
                        <div class="col-md-9">
                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ $bank->opening_balance }}" required>
                            @error('opening_balance')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Balance -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Current Balance') }}</label>
                        <div class="col-md-9">
                            <input type="number" step="0.01" name="current_balance" class="form-control" value="{{ $bank->current_balance }}" required>
                            @error('current_balance')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Country') }}</label>
                        <div class="col-md-9">
                            <input type="text" name="country" class="form-control" value="{{ $bank->country }}" required>
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
@endsection
