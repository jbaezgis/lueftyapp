<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Post;

class PostsController extends Controller {
	public function index(){
		$posts = Post::paginate(10);
		return view('posts.index', compact('posts'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$post = Post::findOrFail($id);
		return view('posts.single', compact('post'));
	}
}
