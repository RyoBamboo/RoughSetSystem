@extends('base')

@section('css')
    <link rel="stylesheet" type="text/css" href={{asset("/assets/lib/uikit/css/components/progress.gradient.css")}}>
    <link rel="stylesheet" type="text/css" href={{asset("/assets/lib/uikit/css/components/slideshow.css")}}>
@endsection

@section('js')
        <script src="/assets/js/lib/uikit/components/slideshow.min.js"></script>
@endsection

@section('content')
    @if (isset($_COOKIE['is_tutorial'])) @include('graph.tutorial_modal') @endif
    <div class="tm-menubar uk-width-1-1">
        @include('graph.menubar')
    </div>
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Graph</h1><br>
                <table class="uk-table">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>内容</th>
                        <th>作成日時</th>
                        <th>更新日時</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->created }}</td>
                            <td>{{ $item->updated }}</td>
                            <td>
                                <a href="/graph/view/{{ $item->id }}" class="uk-button uk-button-small uk-button-primary">グラフを見る</a>
                                <a id="make-modal" data-id={{ $item->id }} href="#my-id" data-uk-modal class="uk-button uk-button-small">レビューを分析する</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('graph.make_modal')
@endsection