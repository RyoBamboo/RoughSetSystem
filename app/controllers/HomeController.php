<?php

class HomeController extends BaseController {

    public function __construct()
    {
        $this->data['pagename'] = 'home';
    }

	public function index()
	{
		return View::make('home.index')->with($this->data);
	}

}
