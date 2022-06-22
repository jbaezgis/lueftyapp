<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Airplain;

class AirplainController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$airplains = Airplain::orderBy('orden', 'asc')->get();
		return view('admin.airplains.index', ['airplains'	=> $airplains]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.airplains.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'name'		=> 'required|min:2|max:250',
			'maxpassengers' => 'required|numeric|min:1|max:100',
			'picture_url'		=> 'required|image',
			'pilot'		=> 'required|numeric'
		]);

		$input = $request->all();

		$image = $request->file('picture_url');
		$filename = time().$image->getClientOriginalName();
		$path = '/home/privauk0/public_html/dominicanshuttles.com/airplains/';
		$image->move($path, $filename);

		$input['picture_url'] = $filename;
		$airplain = Airplain::create($input);

		return redirect('admin/airplains')->with('info', 'Airplain created successful!');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$airplain = Airplain::find($id);
		return view('admin.airplains.edit', ['airplain'	=> $airplain]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'name'		=> 'required|min:2|max:250',
			'maxpassengers' => 'required|numeric|min:1|max:100',
			'pilot'		=> 'required|numeric'
		]);

		$airplain = Airplain::find($id);
		$airplain->update($request->all());

		if ($request->hasFile('picture_url')){
			$oldimage = public_path() . '/airplains/'.$airplain->picture_url;
			@unlink($oldimage);

			$image 		= $request->file('picture_url');
			$filename 	= time().$image->getClientOriginalName();
			$path 	=   public_path() . '/airplains/';
			$image->move($path, $filename);

			$airplain->picture_url = $filename;
			$airplain->save();
		}

		return redirect('admin/airplains')->with('info', 'Airplain updated successful!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$site = Airplain::find($id);
		$site->delete();

		return redirect('admin/airplains')->with('info', 'site removed successful!');
	}
}
