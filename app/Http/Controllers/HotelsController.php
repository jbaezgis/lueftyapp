<?php namespace App\Http\Controllers;

class HotelsController extends Controller {

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        return view('hotels');
    }


}
