<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$testimonials = Testimonial::where('approved', 1)->get();
		return view('testimonials.index')->with('testimonials', $testimonials);
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'author'	=> 'required',
			'quote'		=> 'required|max:400|min:20'
		]);

		Testimonial::create([
			'author'	=> $request->input('author'),
			'quote'		=> $request->input('quote')
		]);

		return redirect()->back()->with('info', 'Thanks for testimonial, we\'ll review it shortly');
	}
}
