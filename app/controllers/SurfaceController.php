<?php

class SurfaceController extends BaseController {

    protected $surface_gestion;

    public function __construct(Surface $surface_gestion)
    {
        $this->surface_gestion = $surface_gestion;
    }


    public function getIndex()
    {
        $no = 1;

        $surfaces = Surface::all();

        foreach ($surfaces as $surface) {
            $surface->no = $no;
            $no ++;
        }

        return View::make('surface_index', array('surfaces' => $surfaces));
    }

    public function getAdd()
    {

        return View::make('surface_add');
    }

    public function postAdd()
    {

        $params = Input::all();

        if ($params['surface-csv-file']) {

            Surface::insertSurfaceByCsv($_FILES['surface-csv-file']);

        } else {

            Surface::insert(array('content'=> $params['surface-content'], 'created_at'=> time(), 'updated_at' => time()));

        }

        return Redirect::to('/surface');
    }

    public function getDelete($surface_id)
    {
        Surface::where('id', '=', $surface_id)->delete();

        return Redirect::to('/surface');
    }

}
