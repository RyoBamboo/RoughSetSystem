<?php

class Item extends Eloquent {

    protected $table = 'items';

    // create発行する際に必要
    protected $guarded = array('id');

    public $timestamps = false;

}
