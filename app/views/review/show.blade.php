@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('review.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Review</h1><br>
                {{ $reviews->links() }}
                <table class="uk-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>内容</th>
                            <th>評価</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->content }}</td>
                            <td>{{ $review->is_bought }}</td>
                            <td><a class="uk-button uk-button-danger" href="/review/del/{{ $review->id }}">削除</a></td>
                            <td><a class="uk-button uk-button-danger" href="/review/del/{{ $review->id }}">削除</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection