<?php

/* CSRFフィルタ */
/*
2015/05/03
これやるとgrap/viewの時エラーなるから対策考える
Route::when('*', 'csrf', ['post']);
*/

/* Home */
Route::get('/', 'HomeController@index');


/* Review */
Route::controller('review', 'ReviewController');


/* Surfacec */
Route::controller('surface', 'SurfaceController');


/* Graph */
Route::group(['prefix'=>'graph'], function() {
    Route::get('', [
        'as'=>'graph',
        'uses'=>'GraphController@index'
    ]);

    Route::get('diff', [
        'as'=>'graph.diff',
        'uses'=>'GraphController@diff'
    ]);

    Route::get('view/{id?}', [
        'as'=>'graph.view',
        'uses'=>'GraphController@view'
    ]);

//    Route::get('make/{id?}', [
//        'as'=>'graph.make',
//        'uses'=>'GraphController@make'
//    ]);

    Route::post('make', [
        'as'=>'graph.make',
        'uses'=>'GraphController@make'
    ]);

    Route::post('load', [
        'as'=>'graph.load',
        'uses'=>'GraphController@load'
    ]);

    Route::post('test', [
        'as'=>'graph.test',
        'uses'=>'GraphController@test'
    ]);

    Route::get('testView', [
        'as'=>'graph.testView',
        'uses'=>'GraphController@testView'
    ]);

    Route::get('graphTest', [
        'as'=>'graph.graphTest',
        'uses'=>'GraphController@graphTest'
    ]);
});


/* Thesaurus */
Route::group(['prefix'=>'thesaurus'], function() {
    Route::get('', [
        'as'=>'thesaurus',
        'uses'=>'ThesaurusController@index'
    ]);

    Route::get('add', [
        'as'=>'add',
        'uses'=>'ThesaurusController@add'
    ]);

    Route::get('delete/{id?}', [
        'as'=>'delete',
        'uses'=>'ThesaurusController@delete'
    ]);

    Route::post('store', [
        'as'=>'store',
        'uses'=>'ThesaurusController@store'
    ]);

    Route::post('upload', [
        'as'=>'upload',
        'uses'=>'ThesaurusController@upload'
    ]);
});


/* Chunk */
Route::group(['prefix'=>'chunk'], function() {
    Route::get('', [
        'as'=>'chunk',
        'uses'=>'ChunkController@index'
    ]);

    Route::get('add', [
        'as'=>'chunk.add',
        'uses'=>'ChunkController@add'
    ]);

    Route::post('store', [
        'as'=>'chunk.store',
        'uses'=>'ChunkController@store'
    ]);

    Route::post('update', [
        'as'=>'chunk.update',
        'uses'=>'ChunkController@update'
    ]);
});


/* Setting */
Route::group(['prefix'=>'setting'], function() {
    Route::get('', [
        'as'=>'setting',
        'uses'=>'SettingController@index'
    ]);

    Route::get('review', [
        'as'=>'setting.review',
        'uses'=>'SettingController@review'
    ]);

    Route::post('storeReview', [
        'as'=>'setting.storeReview',
        'uses'=>'SettingController@storeReview'
    ]);
});

/* API */
Route::post('/api/twitter/get', 'ReviewController@twitter');

/* Test */
Route::controller('test', 'TestController');
