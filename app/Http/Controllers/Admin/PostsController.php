<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Post;

class PostsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$posts = Post::all();
		return view('admin.posts.index', ['posts'	=> $posts]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return view('admin.posts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore(Request $request)
	{
		$this->validate($request, [
			'title'		=> 'required|min:1|max:250',
			'content'		=> 'required',
			'type'		=> 'required'
		]);

		$input = $request->all();
		unset($input['image_url']);
		$post = Post::create($input);

		if ($request->hasFile('image_url')){
			$image 		= $request->file('image_url');
			$filename 	= time().$image->getClientOriginalName();
			$path 	= 	public_path('posts/');
			$image->move($path, $filename);

			$post->image_url = 'posts/'.$filename;
			$post->save();
		}

		return redirect('admin/posts')->with('info', 'Post created successful!');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$post = Post::find($id);
		return view('admin.posts.edit', compact('post'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postUpdate(Request $request, $id)
	{
		$this->validate($request, [
			'title'		=> 'required|min:1|max:250',
			'content'		=> 'required'
		]);

		$post = Post::find($id);
		$input = $request->all();
		unset($input['image_url']);

		$post->update($input);

		if ($request->hasFile('image_url')){
			$oldimage = public_path($post->image_url);
			@unlink($oldimage);

			$image 		= $request->file('image_url');
			$filename 	= time().$image->getClientOriginalName();
			$path 	= 	public_path('posts/');
			$image->move($path, $filename);

			$post->image_url = 'posts/'.$filename;
			$post->save();
		}

		return redirect('admin/posts')->with('info', 'Post updated successful!');
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		$post = Post::find($id);
		$post->delete();

		return redirect('admin/posts')->with('info', 'Post removed successful!');
	}

}
