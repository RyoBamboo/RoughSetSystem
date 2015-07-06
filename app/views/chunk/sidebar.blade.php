<ul class="tm-nav uk-nav">
    <li class="uk-nav-header">Contents</li>
    <li @if ($pageaction == 'index') class="uk-active" @endif ><a href="/chunk">係受け構文一覧</a></li>
    <li @if ($pageaction == 'add') class="uk-active" @endif ><a href="/chunk/add">新規登録</a></li>
    <li @if ($pageaction == 'chunk') class="uk-active" @endif ><a href="/setting/chunk">設定</a></li>
</ul>

