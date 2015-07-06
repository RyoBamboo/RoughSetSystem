<?php

class Setting extends Eloquent {

    protected $table = 'settings';

    // create発行する際に必要
    protected $guarded = array('id');

    public $timestamps = false;
}
