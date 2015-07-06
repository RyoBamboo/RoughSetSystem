<ul class="tm-nav uk-nav">
    <li class="uk-nav-header">Contents</li>
    <li @if ($pageaction == 'index') class="uk-active" @endif ><a href="/thesaurus">感性ワード一覧</a></li>
    <li @if ($pageaction == 'add') class="uk-active" @endif ><a href="/thesaurus/add">新規感性ワード登録</a></li>
    <li @if ($pageaction == 'thesurus') class="uk-active" @endif ><a href="/setting/thesaurus">設定</a></li>
</ul>