@extends('backend.layouts.app')


@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ translate('Shipment Details') }}</h5>
            <a href="{{ route('backend.shipments.index') }}" class="btn btn-secondary">{{ translate('Back to Shipments List') }}</a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{ translate('Name') }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $shipment->name }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shipment_note">{{ translate('Shipment Note') }}</label>
                        <textarea name="shipment_note" id="shipment_note" class="form-control" rows="3" readonly>{{ $shipment->shipment_note }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('backend.shipments.edit', $shipment->id) }}" class="btn btn-warning">{{ translate('Edit Shipment') }}</a>
            <form action="{{ route('backend.shipments.destroy', $shipment->id) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ translate('Delete Shipment') }}</button>
            </form>
        </div>
    </div>
@endsection
