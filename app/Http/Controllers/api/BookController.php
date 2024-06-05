<?php

namespace App\Http\Controllers\api;

use App\Models\Book;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use App\Http\Requests\StoreBookRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Support\Facades\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all Book collections from the database
        return new BookCollection(Book::all());
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
    public function store(StoreBookRequest $request)
    {
        //create new book after validated the book validation process
        //only authorize user can create new book
        $user = JWTAuth::parseToken()->authenticate();
        $bookData = $request->all();
        $bookData['user_id'] = $user->id;

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('upload/books');
            $fileName = $file->getClientOriginalName();
            $bookData['file_name'] = $fileName;
            $bookData['file_path'] = $filePath;
        }
        $book = Book::create($bookData);
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get the Book record from the database by the ID
        return new BookResource(Book::findOrFail($id));
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
    public function update(UpdateBookRequest $request, $id)
    {
        // Update the Book record from the database by the ID
        // only the book owner is allowed to to update the Book record.
        $user = JWTAuth::parseToken()->authenticate();
        $book = Book::findOrFail($id);
        if ($book->user_id !== $user->id) {
            return Response::fail("Unauthorized", 403);
        }

        $bookData = $request->all();
        $bookData['user_id'] = $user->id;

        // if ($request->hasFile('file')){
        //     if ($book->file_path) {
        //         Storage::delete($book->file_path);
        //     }
        //     $file = $request->file('file');
        //     $filePath = $file->store('upload/books');
        //     $fileName = $file->getClientOriginalName();
        //     $bookData['file_name'] = $fileName;
        //     $bookData['file_path'] = $filePath;
        // }
        $book->update($bookData);
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete the Book record from the database by the ID
        // only the book owner is allowed to delete the book record
        $user = JWTAuth::parseToken()->authenticate();
        $book = Book::findOrFail($id);
        if ($book->user_id !== $user->id) {
            return Response::fail("Unauthorized", 403);
        }
        if ($book->file_path) {
            Storage::delete($book->file_path);
        }
        $book->delete();
        return Response::success("Book Deleted Successfully", 200);
    }
}
