@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('review.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <article class="uk-article">
                    <h1 class="uk-article-title">Review</h1>
                    <p class="uk-article-meta">...</p>
                    <p class="uk-article-lead">...</p>
                    <hr class="uk-article-divider">
                </article>
                <table class="uk-table">
                    <tbody>
                    @foreach($tweets as $tweet)
                        <tr>
                            <td>{{ $tweet }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection