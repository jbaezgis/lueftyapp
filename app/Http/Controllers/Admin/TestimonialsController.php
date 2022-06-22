<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$testimonials = Testimonial::all();
		return view('admin.testimonials.index', ['testimonials'	=> $testimonials]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		return view('admin.testimonials.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function postStore(Request $request)
	{
		$this->validate($request, [
			'author'		=> 'required|min:1|max:250',
			'quote'		=> 'required'
		]);

		$input = $request->all();
		$input['approved'] = 1;
		$testimonial = Testimonial::create($input);

		return redirect('admin/testimonials')->with('info', 'Testimonial created successful!');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		$testimonial = Testimonial::find($id);
		return view('admin.testimonials.edit', compact('testimonial'));
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
			'author'		=> 'required|min:1|max:250',
			'quote'		=> 'required'
		]);

		$testimonial = Testimonial::find($id);
		$testimonial->update($request->all());

		return redirect('admin/testimonials')->with('info', 'Testimonial updated successful!');
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		$testimonial = Testimonial::find($id);
		$testimonial->delete();

		return redirect('admin/testimonials')->with('info', 'Testimonial removed successful!');
	}

}
