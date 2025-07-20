@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Shipments') }}</h1>
            </div>
            @can('add_shipment')
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('backend.shipments.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Shipment') }}</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <div class="card">
        <form id="sort_shipments" action="" method="GET">
            <div class="d-sm-flex justify-content-between mx-4">
                <div class="mt-3">
                    <input type="text" class="form-control form-control-sm h-100" name="search"
                        @isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type & Enter to Search') }}">
                </div>
            </div>
        </form>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Shipment Name') }}</th>
                        <th>{{ translate('Shipment Note') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shipments as $key => $shipment)
                        <tr>
                            <td>{{ $key + 1 + ($shipments->currentPage() - 1) * $shipments->perPage() }}</td>
                            <td>{{ $shipment->name }}</td>
                            <td>{{ $shipment->shipment_note }}</td>
                            <td class="text-right">
                                <!-- Show Button -->
                                <a href="{{ route('backend.shipments.show', $shipment->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('View Details') }}">
                                    <i class="las la-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('backend.shipments.edit', $shipment->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>

                                <!-- Attach Button -->
                                <a href="{{ route('backend.shipments.attach', $shipment->id) }}" class="btn btn-soft-warning btn-icon btn-circle btn-sm" title="{{ translate('Attach') }}">
                                    <i class="las la-paperclip"></i>
                                </a>

                                <!-- Delete Button -->
                                @can('delete_shipment')
                                    <form action="{{ route('backend.shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('{{ translate('Are you sure you want to delete this shipment?') }}');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $shipments->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
