@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12">
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 h6">{{ translate('Gallery Albums') }}</h5>

                    <!-- Search Bar -->
                    <form method="GET" action="{{ route('backend.gallery.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Search albums..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row">
                        @foreach($galleries as $gallery)
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card shadow-sm border-0">
                                    <!-- Album Image -->
                                    @if(!empty($gallery->images) && count(json_decode($gallery->images) ?? []) > 0)
                                        <img src="{{ url('public/gallery/' . json_decode($gallery->images)[0]) }}" class="card-img-top img-fluid" alt="{{ $gallery->title }}">

                                    @else
                                        <img src="{{ url('default-placeholder.jpg') }}" class="card-img-top img-fluid" alt="No Image">
                                    @endif


                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ $gallery->title }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($gallery->description, 50) }}</p>

                                        <!-- View Album -->
                                        <a href="{{ route('backend.gallery.show', $gallery->id) }}" class="btn btn-primary btn-sm">
                                            {{ translate('View') }}
                                        </a>

                                        <!-- Edit Album -->
                                        <a href="{{ route('backend.gallery.edit', $gallery->id) }}" class="btn btn-warning btn-sm">
                                            {{ translate('Edit') }}
                                        </a>

                                        <!-- Delete Album -->
                                        <form action="{{ route('backend.gallery.destroy', $gallery->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                {{ translate('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- No Albums Found -->
                    @if($galleries->isEmpty())
                        <p class="text-center text-muted">{{ translate('No albums found.') }}</p>
                    @endif
                </div>

                <!-- Pagination -->
                <div class="card-footer d-flex justify-content-center">
                    {{ $galleries->appends(['search' => request('search')])->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
