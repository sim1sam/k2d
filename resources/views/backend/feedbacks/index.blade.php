@extends('backend.layouts.app')

@section('content')
    <div class="container">
        <h1>{{ translate('Feedback Submissions') }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ translate('Name') }}</th>
                        <th>{{ translate('Mobile Number') }}</th>
                        <th>{{ translate('Service Rating') }}</th>
                        <th>{{ translate('Suggestion') }}</th>
                        <th>{{ translate('Birthday') }}</th>
                        <th>{{ translate('Anniversary') }}</th>
                        <th>{{ translate('Submitted At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ratingTexts = [
                            1 => translate('Very Poor'),
                            2 => translate('Poor'),
                            3 => translate('Not Good Not Bad'),
                            4 => translate('Satisfactory'),
                            5 => translate('Impressive')
                        ];
                    @endphp
                    @foreach($feedbacks as $feedback)
                        <tr>
                            <td>{{ $feedback->name }}</td>
                            <td>{{ $feedback->mobile_number }}</td>
                            <td>{{ $ratingTexts[$feedback->service_rating] ?? translate('Unknown') }}</td>
                            <td>{{ $feedback->suggestion }}</td>
                            <td>{{ $feedback->birthday }}</td>
                            <td>{{ $feedback->anniversary }}</td>
                            <td>{{ $feedback->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="d-flex justify-content-center">
            {{ $feedbacks->links() }}
        </div>
    </div>
@endsection