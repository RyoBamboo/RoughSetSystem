@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('chunk.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Chunk</h1><br>
                {{--{{ $items->links(); }}--}}
                <table class="uk-table">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>from</th>
                        <th>to</th>
                        <th>ネガポジ判定</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($chunks as $chunk)
                        <tr>
                            <td>{{ $chunk->id }}</td>
                            <td>{{ $chunk->from }}</td>
                            <td>{{ $chunk->to }}</td>
                            <td>{{ $chunk->nega_posi }}</td>
                            <td>
                                {{-- MEMO: ボタンの文字列はそのままAjaxのパラメータに使われているので注意 --}}
                                <button data-id="{{$chunk->id}}" class="uk-button btn-chunk" @if ($chunk->nega_posi == 'p') disabled @endif >p</button>
                                <button data-id="{{$chunk->id}}" class="uk-button btn-chunk" @if ($chunk->nega_posi == 'n') disabled @endif >n</button>
                                <button data-id="{{$chunk->id}}" class="uk-button btn-chunk" @if ($chunk->nega_posi == 'f') disabled @endif >f</button>
                                <button data-id="{{$chunk->id}}" class="uk-button btn-chunk" @if ($chunk->nega_posi == 'non') disabled @endif >non</button>
                            </td
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
