<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\Helper;
use App\Models\User;
use DataTables;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users');
    }

    public function get_users()
    {
        
        $model = User::orderBy('created_at', 'asc')->selectRaw("email, id, name, ( CASE WHEN role=1 THEN 'ADMIN' ELSE 'Non Admin' END) as role")->get();
        return DataTables::of($model)
        ->addColumn('action', function($row){
            $actionBtn = '<button onClick="actionDeleteUsers(`'.Crypt::encrypt($row->id).'`)" class="delete btn btn-danger btn-sm text-white mx-2">Delete</button>';
            return $actionBtn;
        })
        ->rawColumns(['action'])
        ->addIndexColumn()
        ->make(true);
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
            $requestForm = $this->collect_data_add();
            $actionQuery = User::create($requestForm);
            $response = ($actionQuery == true) ? Helper::success($actionQuery, 'Data Berhasil ditambahkan') :  Helper::error(null, 'Data gagal ditambahkan');
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $author = User::findOrfail(Crypt::decrypt($id));
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
            'email' => 'required|string',
            'name' => 'required|string',
            'role' => 'required|integer',
        ]);
        return $schema;
    }
    

    protected function collect_data_add()
    {
        $password = (request()->password != '') ?request()->password : 'Password123';
        $requestForm = [
            'email' => request()->email, 
            'name' => request()->name,
            'role' => request()->role,
            'password' => bcrypt($password),
        ];
        return $requestForm;
    }
}
