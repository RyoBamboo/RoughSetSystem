<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <!-- include CSS -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href={{asset("/assets/lib/uikit/css/uikit.gradient.css")}}>
    <link rel="stylesheet" type="text/css" href={{asset("/assets/css/base.css")}}>
    @yield('css')

    <!-- include Font -->
    <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>

    <!-- include Favicon -->
    <link rel="icon" href={{asset("/assets/img/icon.ico")}} type="image/x-icon">

    <!-- include JS -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="{{ asset("/assets/lib/uikit/js/uikit.js") }}"></script>
    <script type="text/javascript" src="{{ asset("/assets/js/main.js") }}"></script>

    <title>RoughSetTheorySystem</title>
</head>
<body class="tm-background">
    <nav class="tm-navbar uk-navbar uk-navbar-attached">
        <div class="uk-container uk-container-center">
            {{--<a class="uk-navbar-brand" href="/"><img class="uk-margin uk-margin-remove" src="/assets/img/logo2.png" width="90"></a>--}}
            <ul class="uk-navbar-nav">
                <li @if ($pagename == 'review') class="uk-active" @endif><a href="/review">Review</a></li>
                <li @if ($pagename == 'graph') class="uk-active" @endif><a href="/graph">Graph</a></li>
                <li @if ($pagename == 'thesaurus') class="uk-active" @endif><a href="/thesaurus">Thesaurus</a></li>
                <li @if ($pagename == 'chunk') class="uk-active" @endif><a href="/chunk">Chunk</a></li>
                <li @if ($pagename == 'setting') class="uk-active" @endif><a href="/setting">Setting</a></li>
                <li @if ($pagename == 'faq') class="active" @endif><a href="/faq">FAQ</a></li>
            </ul>
        </div>
    </nav>
    <div class="wrapper">
        @include('layout.breadcrumb')
        @include('layout.slidemenu')
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
    @yield('js')
</body>
</html>
