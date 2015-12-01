<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <!-- include CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href={{asset("/assets/lib/uikit/css/uikit.gradient.css")}}>
    <link rel="stylesheet" type="text/css" href={{asset("/assets/css/base.css")}}>
    <link rel="stylesheet" type="text/css" href={{asset("/assets/lib/hamburger/hamburger.css")}}>
    @yield('css')

    <!-- include Font -->
    <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>

    <!-- include Favicon -->
    <link rel="icon" href={{asset("/assets/img/icon.ico")}} type="image/x-icon">

    <!-- include JS -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="{{ asset("/assets/lib/uikit/js/uikit.js") }}"></script>
    <script src="{{ asset("/assets/lib/hamburger/hamburger.js") }}"></script>
    <script type="text/javascript" src="{{ asset("/assets/js/main.js") }}"></script>

    <title>RoughSetTheorySystem</title>
</head>
<body class="tm-background">
    <div id="container">
    <header class="tm-header">
        <div id="hamburger" is-open=true>
            <img src="/assets/img/icon_hamburger.png">
        </div>
    </header>
    <nav class="tm-nav">
        <div class="tm-nav-header">
        </div>
        <ul>
            @if ($pagename == 'graph')
                <li class="tm-nav tm-nav-active"><img class="icon-nav" src="/assets/img/icon_graph_active.png">階層グラフ</li>
            @else
                <li><a href="graph"><img class="icon-nav" src="/assets/img/icon_graph.png">階層グラフ</a></li>
            @endif
            @if ($pagename == 'thesaurus')
                <li class="tm-nav tm-nav-active"><img class="icon-nav" src="/assets/img/icon_dictionary_active.png">感性ワード</li>
            @else
                <li><a href="/thesaurus"><img class="icon-nav" src="/assets/img/icon_dictionary.png">感性ワード</a></li>
            @endif
            @if ($pagename == 'review')
                <li class="tm-nav tm-nav-active"><img class="icon-nav" src="/assets/img/icon_review_active.png">レビュー</li>
            @else
                <li><a href="/review"><img class="icon-nav" src="/assets/img/icon_review.png">レビュー</a></li>
            @endif
            @if ($pagename == 'setting')
                <li class="tm-nav tm-nav-active"><img class="icon-nav" src="/assets/img/icon_setting_active.png">設定</li>
            @else
                <li><a href="/setting"><img class="icon-nav" src="/assets/img/icon_setting.png">設定</a></li>
            @endif
            @if ($pagename == 'help')
                <li class="tm-nav tm-nav-active"><img class="icon-nav" src="/assets/img/icon_help_active.png">ヘルプ</li>
            @else
                <li><a href="/faq"><img class="icon-nav" src="/assets/img/icon_help.png">ヘルプ</a></li>
            @endif
        </ul>
    </nav>
<!--     {{--<nav class="tm-navbar uk-navbar uk-navbar-attached">--}}
        {{--<div class="uk-container uk-container-center">--}}
            {{--<a class="uk-navbar-brand" href="/"><img class="uk-margin uk-margin-remove" src="/assets/img/logo2.png" width="90"></a>--}}
            {{--<ul class="uk-navbar-nav">--}}
                {{--<li @if ($pagename == 'review') class="uk-active" @endif><a href="/review">Review</a></li>--}}
                {{--<li @if ($pagename == 'graph') class="uk-active" @endif><a href="/graph">Graph</a></li>--}}
                {{--<li @if ($pagename == 'thesaurus') class="uk-active" @endif><a href="/thesaurus">Thesaurus</a></li>--}}
                {{--<li @if ($pagename == 'chunk') class="uk-active" @endif><a href="/chunk">Chunk</a></li>--}}
                {{--<li @if ($pagename == 'setting') class="uk-active" @endif><a href="/setting">Setting</a></li>--}}
                {{--<li @if ($pagename == 'faq') class="active" @endif><a href="/faq">FAQ</a></li>--}}
            {{--</ul>--}}
        {{--</div>--}}
    {{--</nav>--}}
 -->   
  <div class="wrapper">
        <div class="tm-middle">
            @if (Session::has('message'))
                <div class="uk-alert uk-alert-success">{{ Session::get('message') }}</div>
            @endif
            @yield('content')
        </div>
        <div class="tm-footer">
            <div class="uk-container uk-container-center uk-text-center">
                <div class="uk-panel">
                    <p>Made by Ryo Takenoshita <br>Licensed under Design System Lab.</p>
                </div>
            </div>
        </div>
    </div>
    </div>

    @yield('js')
</body>
</html>
