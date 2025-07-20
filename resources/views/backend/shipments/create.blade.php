@extends('backend.layouts.app')


@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ translate('Add New Shipment') }}</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('backend.shipments.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">{{ translate('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="shipment_note">{{ translate('Shipment Note') }}</label>
                    <textarea name="shipment_note" id="shipment_note" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
            </form>
        </div>
    </div>
@endsection
