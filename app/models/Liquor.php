<?php

class Liquor extends Eloquent {

    protected $table = 'liquors';

    // create発行する際に必要
    protected $guarded = array('id');


}
