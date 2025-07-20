@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Offers') }}</h1>
            </div>

            @can('add_offer')
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('backend.offers.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Offer') }}</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <div class="card">
        <form id="sort_offers" action="" method="GET">
            <div class="d-sm-flex justify-content-between mx-4">
                <div class="mt-3">
                    <input type="text" class="form-control form-control-sm h-100" name="search"
                           @isset($sort_search) value="{{ $sort_search }}" @endisset
                           placeholder="{{ translate('Type & Enter to Search') }}">
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table aiz-table mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Offer Title') }}</th>
                        <th>{{ translate('Description') }}</th>
                        <th>{{ translate('Discount (%)') }}</th>
                        <th>{{ translate('Start Date') }}</th>
                        <th>{{ translate('End Date') }}</th>
                        <th>{{ translate('Image') }}</th>
                        <th>{{ translate('Status') }}</th> <!-- Added Status Column -->
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($offers as $key => $offer)
                        <tr>
                            <td>{{ $key + 1 + ($offers->currentPage() - 1) * $offers->perPage() }}</td>
                            <td>{{ $offer->title }}</td>
                            <td>{{ Str::limit($offer->description, 50) }}</td>
                            <td>{{ number_format($offer->discount, 2) }}%</td>
                            <td>{{ $offer->start_date }}</td>
                            <td>{{ $offer->end_date }}</td>
                            <td>
                            @if($offer->image)
                                <!-- Image Thumbnail -->
                                    <img src="{{ asset('public/' . $offer->image) }}" alt="Offer Image" width="50" height="50" data-toggle="modal" data-target="#imageModal{{ $offer->id }}">

                                    <!-- Modal -->
                                    <div class="modal fade" id="imageModal{{ $offer->id }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageModalLabel">{{ translate('Offer Image') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Full-screen Image -->
                                                    <img src="{{ asset('public/' . $offer->image) }}" alt="Offer Image" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{ translate('No Image') }}
                                @endif
                            </td>
                            <td>
                                @if($offer->status == 'active')
                                    <span class="text-success">{{ translate('Active') }}</span>
                                @elseif($offer->status == 'inactive')
                                    <span class="text-danger">{{ translate('Deactive') }}</span>
                                @else
                                    <span class="text-secondary">{{ ucfirst($offer->status) }}</span>
                                @endif
                            </td>

                            <td class="text-right">
                                <!-- Show Button -->
                                <a href="{{ route('backend.offers.show', $offer->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('View Details') }}">
                                    <i class="las la-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('backend.offers.edit', $offer->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>

                                <!-- Delete Button -->
                                @can('delete_offer')
                                    <form action="{{ route('backend.offers.destroy', $offer->id) }}" method="POST" onsubmit="return confirm('{{ translate('Are you sure you want to delete this offer?') }}');" style="display: inline;">
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
            </div>

            <div class="aiz-pagination">
                {{ $offers->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
