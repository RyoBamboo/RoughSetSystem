<div class="tm-menubar-title">
	<img src="/assets/img/icon_graph_title.png"><h2>階層グラフ</h2>
</div>
<ul>
	<li @if($pageaction == 'index') class="active" @endif ><a href="/graph">単体グラフ</a></li>
	<li @if($pageaction == 'diff') class="active" @endif ><a href="/graph/diff">差分グラフ</a></li>
	<li @if($pageaction == 'setting') class="active" @endif ><a href="/setting/graph">設定</a></li>
</ul>

