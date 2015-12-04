<?php

class TutorialController extends \BaseController {

    public function __construct()
    {
        setcookie('is_tutorial', true);
    }


    public function index()
    {
        return Redirect::to('/review');
    }


}
