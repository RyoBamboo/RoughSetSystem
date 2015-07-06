@extends('layout.base')

@section('content')
<form role="form" action="/surface/add" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="form-group col-md-2">
            <label for="item-name">感性ワード</label>
            <input type="text" name="surface-content" class="form-control" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <label>CSVファイルから読み込む</label>
        <input type="file" name="surface-csv-file" id="test">
        <p class="help-block">csvファイルのみ対応</p>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary btn-sm" value="登録">
    </div>
</form>
@stop

