<div class="tm-menubar-title">
	<img src="/assets/img/icon_graph_title.png"><h2>レビュー</h2>
</div>
<ul>
	<li @if($pageaction == 'index') class="active" @endif ><a href="/review">レビュー対象一覧</a></li>
	<li @if($pageaction == 'add') class="active" @endif ><a href="/review/add">新規レビュー対象登録</a></li>
	<li @if($pageaction == 'setting') class="active" @endif ><a href="/review/setting">設定</a></li>
</ul>

