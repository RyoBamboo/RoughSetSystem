@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('graph.sidebar')
            </div>
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
                                <a href="/graph/make/{{ $item->id }}" class="uk-button uk-button-small">グラフを作成する</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection