@extends('base')

@section('css')
    <link rel="stylesheet" type="text/css" href="/assets/css/graph.css">
@stop

@section('js')
    <script src="/assets/js/d3.v2.js"></script>
    <script src="/assets/js/util.js"></script>
    <script src="/assets/js/diff_graph.js"></script>
@stop

@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('graph.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <!-- ここにグラフ描画 -->
                <div id="graph"></div>
            </div>
        </div>
    </div>
    <!-- レビュー詳細表示用モーダル -->
    <div class="uk-modal">
        <div class="uk-modal-dialog">
            <h3>レビュー</h3>
            <p class="review_text"></p>
            <h3>要点</h3>
            <p class="chunk_text"></p>
        </div>
    </div>
@stop

