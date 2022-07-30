<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\Helper;
use DataTables;
use Validator;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('author');
    }

    public function get_authors()
    {
        $model = Author::orderBy('created_at', 'desc')->selectRaw("CONCAT(first_name,' ', last_name) as fullname, id, short_description, DATE_FORMAT(date_of_birth, '%d %M %Y') as date_of_birth")->get();
        return DataTables::of($model)
        ->addColumn('action', function($row){
            $actionBtn = '-';
            if(auth()->user()->role == 1){
                $actionBtn = '<button  class="edit btn btn-success btn-sm mx-2" onClick="actionShowAuthorDetail(`'.Crypt::encrypt($row->id).'`)">Edit</button>';
                $actionBtn .= '<button onClick="actionDeleteAuthor(`'.Crypt::encrypt($row->id).'`)" class="delete btn btn-danger btn-sm text-white mx-2">Delete</button>';
            }
            return $actionBtn;
        })
        ->rawColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    /**
     * Display data from keyword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function find_author_by_name(Request $request)
    {
        $keyword = $request->keyword;
        $author = Author::selectRaw("id, CONCAT(first_name,' ', last_name) as text")
                    ->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->limit(5)->get();
        return Helper::success($author, 'Data Berhasil ditemukan');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $schema = $this->rules_validation();
        if ($schema->fails()) {
            $response = Helper::error(null, $schema->errors());
        }else{
            $requestForm = $this->collect_data();
            $actionQuery = Author::create($requestForm);
            $response = ($actionQuery == true) ? Helper::success($actionQuery, 'Data Berhasil ditambahkan') :  Helper::error(null, 'Data gagal ditambahkan');
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(request()->ajax()){
            $author = Author::findOrfail(Crypt::decrypt($id));
            $response = (!$author) ? Helper::error(null, 'Data tidak ditemukan') : Helper::success($author, 'Data Berhasil ditemukan');
        }else{
            $response = Helper::error(null, 'Unauthorize', 403);
        }
        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $schema = $this->rules_validation();
        if ($schema->fails()) {
            $response = Helper::error(null, $schema->errors());
        }else{
            $requestForm = $this->collect_data();
            $actionQuery = Author::where(['id' => $id])->update($requestForm);
            $response = ($actionQuery == true) ? Helper::success($actionQuery, 'Data Berhasil diubah') :  Helper::error(null, 'Data gagal diubah');
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $author = Author::findOrfail(Crypt::decrypt($id));
        if(!$author){
            $response = Helper::error(null, 'Data tidak ditemukan');
        }else{
            $author->delete();
            $response = Helper::success(null, 'Data berhasil dihapus');
        }
        return $response;
    }


    protected function rules_validation()
    {
        $schema = Validator::make(request()->all() , [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'short_description' => 'required|string',
        ]);
        return $schema;
    }

    protected function collect_data()
    {
        $requestForm = [
            'first_name' => request()->first_name, 
            'last_name' => request()->last_name,
            'date_of_birth' => request()->date_of_birth,
            'short_description' => request()->short_description,
        ];
        return $requestForm;
    }
}
