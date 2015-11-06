<div id="rayer"></div>
<nav class="slide_menu">
    <div class="slide_menu_header">
    </div>
    <ul class="tm-navbar uk-list">
        <li @if ($pagename == 'review') class="uk-active" @endif><a href="/review">Review</a></li>
        <li @if ($pagename == 'graph') class="uk-active" @endif><a href="/graph">Graph</a></li>
        <li @if ($pagename == 'thesaurus') class="uk-active" @endif><a href="/thesaurus">Thesaurus</a></li>
        <li @if ($pagename == 'chunk') class="uk-active" @endif><a href="/chunk">Chunk</a></li>
        <li @if ($pagename == 'setting') class="uk-active" @endif><a href="/setting">Setting</a></li>
        <li @if ($pagename == 'faq') class="active" @endif><a href="/faq">FAQ</a></li>
    </ul>
</nav>
{{--<button id="button"><i class="fa fa-bars"></i> Menu</button>--}}