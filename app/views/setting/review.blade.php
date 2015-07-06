@extends('base')
@section('content')
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="tm-sidebar uk-width-2-10">
                @include('setting.sidebar')
            </div>
            <div class="tm-main uk-width-8-10">
                <h1 class="uk-article-title">Setting</h1><br>
                {{ Form::open(array('url'=>url('/setting/storeReview'), 'class'=>'uk-form')) }}
                    <fieldset data-uk-margin>
                        <legend>レビュー自動抽出設定</legend>
                        <div class="uk-form-row">
                            {{ Form::label('frequency', '頻度 :') }}
                            {{ Form::select('frequency', array('day'=>'毎日', 'week'=>'毎週', 'month'=>'毎月'), empty($setting) ? 'day' : $setting->frequency)  }}
                        </div>

                        <div class="uk-form-row">
                            {{ Form::label('time', '時間 :') }}
                            {{ Form::select('hour', $hours, empty($setting) ? 0 : $setting->hour) }}
                            {{ Form::label('hour', '時 ') }}
                            {{ Form::select('minute', $minutes, empty($setting) ? 0 : $setting->minute) }}
                            {{ Form::label('minute', '分 ') }}
                        </div>
                        <br>
                        <legend>レビューNGワード設定</legend>
                        <p><span class="uk-badge">NOTE</span><br>NGワードを設定するとそのワードを含んだレビューを抽出対象から外すことができます。 カンマ(,)区切りで複数指定</p>
                        <div class="uk-form-row">
                            {{ Form::textarea('ng-word', $setting->ng_word) }}
                        </div>
                        <div class="uk-form-row">
                            <button type='submit' class="uk-button uk-button-primary">更新</button>
                        </div>
                    </fieldset>
                    {{ Form::hidden('id', empty($setting) ? null : $setting->id) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
