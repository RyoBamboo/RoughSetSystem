<?php

class ItemController extends BaseController {

    public function __construct()
    {
        $this->data['pagename'] = 'item';
        // $last_result = json_decode(File::get(public_path() . '/assets/dat/97/syntactic.dat'), true);
        // var_dump($last_result);exit;

    }

    public function index()
    {

    }

    public function detail($item_id)
    {
        $this->data['item_id'] = $item_id;
        return View::make('item.detail', $this->data);
    }

}