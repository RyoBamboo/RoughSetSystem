<?php

class ChunkController extends Controller {

    private $chunk_gestion;

    public function __construct(Chunk $chunk_gestion)
    {
        $this->chunk_gestion = $chunk_gestion;

        $this->data['pagename'] = 'chunk';
    }

    public function index()
    {
        $chunks = $this->chunk_gestion->orderBy('id', 'desc')->get();


        $this->data['pageaction'] = 'index';
        $this->data['chunks'] = $chunks;

        return View::make('chunk.index', $this->data);
    }

    public function add()
    {
        $this->data['pageaction'] = 'add';
        return View::make('chunk.add', $this->data);
    }

    public function store()
    {
        $input = Input::only('from', 'to', 'nega_posi');
        $rules = array(
            'from' => 'required',
            'to'   => 'required',
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return Redirect::route('chunk.add')->withErrors($validator)->withInput();
        }

        $this->chunk_gestion->create($input);

        return Redirect::route('chunk');
    }

    public function update() {
        // TODO: バリデーション必要
        // エラーがあった際はユーザに知らせる
        $this->chunk_gestion->find(Input::get('id'))->update(array('nega_posi'=>Input::get('type')));
    }
}
