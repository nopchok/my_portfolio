<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = auth()->user()->images;
 
        return response()->json([
            'success' => true,
            'images' => $images
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:png,jpg,jpeg'
        ]);
        
        $name = time().'_'.$request->file->getClientOriginalName();
        $filePath = $request->file('file')->storeAs('uploads', $name, 'public');

        $image = new Image();
        $image->filename= '/storage/' . $filePath;

        if (auth()->user()->images()->save($image)) {
            return response()->json([
                'success' => true,
                'data' => $image->toArray()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Image not added'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        $image = auth()->user()->images()->find($image);
 
        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found '
            ], 400);
        }
 
        return response()->json([
            'success' => true,
            'image' => $image->toArray()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        //
    }
}
