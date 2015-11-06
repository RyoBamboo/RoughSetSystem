@extends('base')

@section('css')
    <link rel="stylesheet" type="text/css" href="/assets/css/graph.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/lib/circliful/circliful.css">
@stop

@section('js')
    <script src="/assets/js/d3.v2.js"></script>
    <script src="/assets/js/util.js"></script>
    <script src="/assets/js/graph.js"></script>
    <script src="/assets/js/lib/uikit/components/accordion.min.js"></script>
    <script src="/assets/js/lib/circliful/circliful.min.js"></script>
@stop

@section('content')
    <div id="header">
        <div id="navimenu">
            <h4>表示条件指定</h4>
            <ul class="menu cf">
                <li class="box" id="rayer1">形態要素に関するルール表示</li>
                <li class="box" id="rayer2">認知に関するルール表示</li>
                <li class="box" id="rayer3">イメージに関するルール表示</li>
                <li class="box" id="rayer4">全ての表示する</li>
                <li class="box" id="menu_hidedr" style="display:none;">全てのルールを表示する</li>
            </ul>
            <br />
            <ul class="menu">
                <li class="box" id="menu_chunk">評価句表示/非表示切り替え</li>
                <li class="box" id="menu_negaposi">評価句のみ表示</li>
                <li class="box" id="menu_posi">ポジティブな評価句のみ表示</li>
                <li class="box" id="menu_nega">ネガティブな評価句のみ表示</li>
            </ul>
        </div>
    </div>
    <div id="main">
        <div id="graph"></div>
        <div id="review" class="infotext" style="display:none;"></div>


        <div id="DR" class="infotext" style="display:none;"></div>
        <div id="DRH" class="infotext" style="display:none;"></div>
        <div id="ATTR" class="infotext" style="display:none;"></div>

        <div id="left_content">
            <ul class="menu">
                <li id="menu_dr">DR</li>
                <li id="menu_drh">決定表</li>
                <li id="menu_attr">属性値情報</li>
            </ul>
        </div>
        <p id="menu_reviews"></p>
        <div id="right_content" style="display:none;">
            <div id="reviews">
                <ul>
                </ul>
            </div>
        </div>
    </div>
    <div id="myStat" data-dimension="200" data-text="50%" data-info="New Clients" data-width="5" data-fontsize="25" data-percent="50" data-fgcolor="#A9E7D0" data-bgcolor="#eee" data-total="100" data-part="50" data-icon="fa-long-arrow-up" data-icon-size="28" data-icon-color="#fff"></div>
    <script>
        $( document ).ready(function() {
            $('#myStat').circliful();
        });
    </script>
@stop


