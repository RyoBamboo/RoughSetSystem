<?php

class ItemController extends BaseController {

    public function __construct()
    {
        $this->data['pagename'] = 'item';
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