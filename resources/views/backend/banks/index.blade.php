@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Banks') }}</h1>
            </div>
            @can('add_bank')
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('backend.banks.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Bank') }}</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <div class="card">
        <form id="sort_banks" action="" method="GET">
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
                        <th>{{ translate('Bank Name') }}</th>
                        <th>{{ translate('Account Number') }}</th>
                        <th>{{ translate('Branch Name') }}</th>
                        <th>{{ translate('Opening Balance') }}</th>
                        <th>{{ translate('Current Balance') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($banks as $key => $bank)
                        <tr>
                            <td>{{ $key + 1 + ($banks->currentPage() - 1) * $banks->perPage() }}</td>
                            <td>{{ $bank->name }}</td>
                            <td>{{ $bank->account_number }}</td>
                            <td>{{ $bank->branch }}</td>
                            <td>{{ number_format($bank->opening_balance, 2) }}</td>
                            <td>{{ number_format($bank->current_balance, 2) }}</td>
                            <td class="text-right">
                                <!-- Show Button -->
                                <a href="{{ route('backend.banks.show', $bank->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('View Details') }}">
                                    <i class="las la-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('backend.banks.edit', $bank->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>

                                <!-- transaction Button -->
                                <a href="{{ route('transactions.index', $bank->id) }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                    <i class="las la-eye"></i>
                                </a>

                                <!-- Delete Button -->
                                @can('delete_bank')
                                    <form action="{{ route('backend.banks.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('{{ translate('Are you sure you want to delete this bank?') }}');" style="display: inline;">
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
                {{ $banks->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
