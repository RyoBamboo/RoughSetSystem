@extends('layout.base')

@section('content')
<form role="form" onsubmit="return false;">
    <div class="row">
        <div class="form-group col-md-2">
            <label for="item-name">登録名</label>
            <input type="text" name="item-name" class="form-control" id="item-name" placeholder="">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-2">
            <label>検索方法</label>
            <select name=search_type" id="search-type" class="form-control">
                <option value="item-keyword">キーワードから検索</option>
                <option value="item-code">レビューコードから検索</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <div class="col-md-2">
                <input type="text" name="item-keyword" class="form-control" id="item-keyword" placeholder="検索ワード">
            </div>
            <div class="col-md-1">
                <button id="item-search" class="btn btn-primary btn-sm">検索</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <table id="resultTable" class="table table-bordered">
            </table>
        </div>
    </div>
</form>
@stop
<!-- include modal -->
@include('modal.get_review_modal')

