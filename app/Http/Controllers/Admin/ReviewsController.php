<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$reviews = Review::orderBy('created_at', 'DESC')->paginate(20);
		return view('admin.reviews.index', compact('reviews'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$options = $this->getOptions();

		return view('admin.reviews.create', compact('options'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore()
	{
		$this->validate($request, [
			'author'	=> 'required|min:2|max:80',
			'email'	=> 'required|email',
			'short_description' => 'required|max:250',
			'rate' => 'required',
			'reuse' => 'required'
		]);

		$input = $request->all();
		Review::create($input);

		return redirect('admin/reviews')->with('info', 'Your review has been submited!');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$review = Review::find($id);
		$options = $this->getOptions();
		return view('admin.reviews.edit', compact('review', 'options'));
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
			'author'	=> 'required|min:2|max:80',
			'email'	=> 'required|email',
			'short_description' => 'required|max:250',
			'rate' => 'required',
			'reuse' => 'required'
		]);

		$review = Review::find($id);
		$review->author = $request->input('author');
		$review->email = $request->input('email');
		$review->short_description = $request->input('short_description');
		$review->rate = $request->input('rate');
		$review->reuse = $request->input('reuse');
		$review->approved = $request->input('approved');
		$review->update();

		return redirect('admin/reviews')->with('info', 'Review updated successful!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		$review = Review::find($id);
		$review->delete();

		return redirect('admin/reviews')->with('info', 'Review removed successful!');
	}

	public function getOptions(){
		$options = [
			'Unnaceptable',
			'Very Poor',
			'Poor',
			'Average',
			'Above Average',
			'Good',
			'Very Good',
			'Excellent'
		];
		return $options;
	}

}
