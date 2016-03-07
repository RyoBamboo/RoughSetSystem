<div class="tm-menubar-title">
	<img src="/assets/img/icon_graph_title.png"><h2>感性ワード</h2>
</div>
<ul>
	<li @if($pageaction == 'index') class="active" @endif ><a href="/thesaurus">感性ワード一覧</a></li>
	<li @if($pageaction == 'add') class="active" @endif ><a href="/thesaurus/add">新規感性ワード登録</a></li>
	<li @if($pageaction == 'setting') class="active" @endif ><a href="/setting/thesaurus">設定</a></li>
	<li @if($pageaction == 'index') class="active" @endif ><a href="/chunk">かかり受け辞書一覧</a></li>
	<li @if($pageaction == 'add') class="active" @endif ><a href="/chunk/add">新規かかり受け語句登録</a></li>
	<li @if($pageaction == 'setting') class="active" @endif ><a href="/chunk/thesaurus">設定</a></li>
</ul>

