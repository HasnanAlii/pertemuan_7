<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookshelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class BookController extends Controller
{
    public function index()
    {
        $data['books'] = Book::all();
        return view('books.index', $data);
    }
    public function create()
    {
        $data['bookshelves'] = Bookshelf::pluck('name', 'id');
        return view('books.create', $data);
    }
    public function store(Request $request)
    {
       $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'year' => 'required|max:2077',
            'publisher' => 'required|max:255',
            'city' => 'required|max:50',
            'cover' => 'required',
            'bookshelf_id' => 'required|max:5',
        ]);
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->storeAs(
                'public/cover_buku',
                'cover_buku_' . time() . '.' . $request->file('cover')->extension()
            );
            $validated['cover'] = basename($path);
        } 
        $book = Book::create($validated);
        if ($book) {
            $notification = array(
                'message' => 'Data buku berhasil disimpan',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Data buku gagal disimpan',
                'alert-type' => 'error'
            );
        }
        return redirect()->route('book')->with($notification);
    }
    public function edit($id){
        // dd($id);
        $data['book'] = Book::find($id);
        $data['bookshelves'] = Bookshelf::pluck('name', 'id');
        return view('books.edit',$data);

    }
    public function destroy($id){
        $data = Book::find($id);
        Storage::delete('app/public/cover_buku/'.$data->cover);
        $berhasil =$data->delete();
        if($berhasil){
            $notification = array(
                'message' => 'Data buku berhasil dihapus',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Data buku gagal dihapus',
                'alert-type' => 'error'
            );
        }
        return redirect()->route('book')->with($notification);
    }
       
    
    public function update($id,Request $request){
        $old = Book::find($id);
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'year' => 'required|max:2077',
            'publisher' => 'required|max:255',
            'city' => 'required|max:50',
            'cover' => 'required',
            'bookshelf_id' => 'required|max:5',
        ]);
        if ($request->hasFile('cover')) {
            if($old ->cover != null){
                Storage::delete('app/public/cover_buku/'.$request->old_cover);
            }
            $path = $request->file('cover')->storeAs(
                'public/cover_buku',
                'cover_buku_' . time() . '.' . $request->file('cover')->extension()
            );
            $validated['cover'] = basename($path);
        } 
        $succes = $old ->update($validated);
        if ($succes) {
            $notification = array(
                'message' => 'Data buku berhasil disimpan',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Data buku gagal disimpan',
                'alert-type' => 'error'
            );
        }
        return redirect()->route('book')->with($notification);
      
}
}