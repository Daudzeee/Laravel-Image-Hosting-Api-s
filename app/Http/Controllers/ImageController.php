<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Models\Image;
use App\Models\UserVerification;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(ImageRequest $request)
    {
	    if ($request->hasFile('image') ) {
		    $image_path = time() . '.' . $request->image->extension();
		    $request->image->move(public_path('images'), $image_path);
	    }
		$image =Image::create([
			'name' => $request->name,
			'type' => $request->type,
			'status' => $request->status,
			'path' => $request->path,
		]);
	    $token = $request->header('Authorization');
	    $user = UserVerification::where('token',$request->$token)->first()->user;
	    $image->user()->attach($user);
	    return response()->json(['message' => "Added Successfully", 'data' => $image]);
    }

    public function delete(Image $image, $request)
    {
	    $token = $request->header('Authorization');
	    $user = UserVerification::where('token',$request->$token)->first()->user;
	    if($image)
	    {
		    $image->delete();
		    return response()->json(['message' => "Image Deleted successfully"]);
	    }
	    return response()->json(['message' => "Image Not Found"]);

    }

	public function search(Request $request)
	{
		$token = $request->header('Authorization');
		$user = UserVerification::where('token',$request->$token)->first()->user;
		$filter =$user->images();
		$images =$this->filter($request, $filter);
		return response()->json(['message' => "Search Result", 'data' => $images]);

	}

	public function list()
	{
		$images=Image::where('status','public')->get();
		return response()->json(['message' => "Search Result", 'data' => $images]);
	}


	public function shareLink(Request $request)
	{
		$token = $request->header('Authorization');
		$user = UserVerification::where('token',$request->$token)->first()->user;
		$image=Image::where('id',$request->image_id)->first();
		$Link=route('image/show/', [$image->id]);
		return response()->json(['message' => "Your Search Matches", 'data' => $Link]);

	}

    public function filter($request,$filter)
    {
	    if ($request->has('name'))
	    {
		    $filter->where('name','like','%'.$request->get('name').'%');
	    }
	    if ($request->has('type'))
	    {
		    $filter->where('type', $request->get('type'));
	    }
	    if ($request->has('status'))
	    {
		    $filter->where('status', $request->get('status'));
	    }
	    return $filter;

    }

}