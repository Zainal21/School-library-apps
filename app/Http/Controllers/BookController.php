<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\Helper;
use DataTables;
use Validator;  

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('book', [
            'next_bookcode' => $this->create_book_code()
        ]);
    }

    public function get_books()
    {
        $model = Book::orderBy('book_code', 'asc')->with(['authors' => function($data){
            $data->selectRaw(" CONCAT(first_name, ' ', last_name) as fullname");
        }])->get();
        return DataTables::of($model)
        ->addColumn('action', function($row){
            $actionBtn = '-';
            if(auth()->user()->role == 1){
                $actionBtn = '<button  class="edit btn btn-success btn-sm mx-2" onClick="actionShowBookDetail(`'.Crypt::encrypt($row->id).'`)">Edit</button>';
                $actionBtn .= '<button onClick="actionDeleteBook(`'.Crypt::encrypt($row->id).'`)" class="delete btn btn-danger btn-sm text-white mx-2">Delete</button>';
            }
            return $actionBtn;
        })
        ->addColumn('authors', function($row){
            $authorName = '';
            if(count((array)$row->authors) > 0){
                foreach ($row->authors as $key => $item) {
                    $authorName .= " " . $item->fullname .". ";
                    
                }
            }
            return $authorName;
        })
        ->rawColumns(['action', 'authors'])
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
            $requestForm = $this->collect_data();
            $actionQuery = Book::create($requestForm);
            $actionQuery->authors()->attach($request->authors);
            $response =  Helper::success(null, 'Data Berhasil disimpan', 201);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with(['authors' => function($data){
            $data->selectRaw("authors.id, CONCAT(first_name, '', last_name) as text");
        }])->findOrfail(Crypt::decrypt($id));
        $response = $book ? Helper::success($book, 'Data berhasil ditemukan') : Helper::error(null, 'Data tidak ditemukan');
        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $schema = $this->rules_validation();
        if ($schema->fails()) {
            $response = Helper::error(null, $schema->errors());
        }else{
            $requestForm = $this->collect_data();
            $actionQuery = Book::findOrfail($id);
            $actionQuery->update($requestForm);
            $actionQuery->authors()->sync($request->authors);
            $response =  Helper::success(null, 'Data Berhasil diubah');
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dec = Crypt::decrypt($id);
        $book = Book::findOrfail($dec);
        if($book){
            $book->delete();
            $book->authors()->detach($dec);
            $response = Helper::success($book, 'Data berhasil dihapus');
        }else{
            $response = Helper::error(null, 'Data data tidak ditemukan');
        }
        return $response;
    }

    protected function create_book_code()
    {
        $month = date('m');
        $years = date('Y');
        $yearFormat = date('y');
        $baseOrdered = "0000";
        $data = Book::selectRaw('max(RIGHT(book_code, 4)) as last_order')->whereMonth('created_at', $month)->whereYear('created_at', $years)->orderBy(DB::raw('max(RIGHT(book_code, 4))', 'DESC'))->take(1)->first();
        if ($data) {
            $baseOrdered = $data->last_order;
        }
        $nextOrdered = abs($baseOrdered) + 1;
        $uniqueCode = 'BOOK' . $month . $yearFormat . sprintf('%04d', $nextOrdered);
        return $uniqueCode;
    }

    protected function rules_validation()
    {
        $schema = Validator::make(request()->all() , [
            'book_code' => 'string|required',
            'title' => 'string|required',
            'page' => 'integer|required',
            'rating' => 'integer|required',
            'description' => 'string|required',
            'published_date' => 'string|required',
            'authors' => 'array'
        ]);
        return $schema;
    }

    protected function collect_data()
    {
        $requestForm = [
            'book_code' => request()->book_code, 
            'title' => request()->title,
            'rating' => request()->rating,
            'description' => request()->description,
            'published_date' => request()->published_date,
            'page' => request()->page
        ];
        return $requestForm;
    }
}
