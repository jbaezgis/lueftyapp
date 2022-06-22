<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\LocationAlias;
use App\Repositories\LocationRepository;

use Illuminate\Http\Request;

class LocationsController extends Controller {
	public $repoLocation;
	
	public function __construct(LocationRepository $LocationRepository){
		parent::__construct();
		$this->repoLocation = $LocationRepository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$locations = Location::paginate(20);
		$locations->setPath('locations');
		return view('admin.locations.index', ['locations'	=> $locations]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.locations.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'location_name'	=> 'required|max:250|unique:locations'
		]);

		//$location = Location::create($request->all());
		$location = $this->repoLocation->save($request->all());

		$locationAlias = $request->input('location_alias');
		if (!empty($locationAlias)){
			$parts = explode("\n", $locationAlias);
			foreach ($parts as $alias){
				if (empty($alias)){continue;}
				
				LocationAlias::create([
					'location_name' => trim($alias),
					'location_id' => $location->id,
					'order_number' => 99
				]);
			}
		}

		return redirect('admin/locations')->with('info', 'Item saved'); 
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$location = Location::findOrFail($id);
		$locationAlias = LocationAlias::where('location_id', $id)->get();
		$locationAliasStr = "";
		foreach ($locationAlias as $alias){
			$locationAliasStr .= $alias->location_name."\n";
		}

		return view('admin.locations.edit', ['location' => $location, 'locationAliasStr' => $locationAliasStr]);
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
			'location_name'	=> 'required|max:250|unique:locations,location_name,'.$id
		]);

		/*$location = Location::findOrFail($id);
		$location->location_name = $request->input('location_name');
		$location->is_airport = $request->has('is_airport') ? 1 : 0;
		$location->save();*/
		
		$data['location_name'] = $request->input('location_name');
		$data['is_airport'] = $request->has('is_airport') ? 1 : 0;
		$location = $this->repoLocation->update($id, $data);

		LocationAlias::where('location_id', $id)->delete();

		$locationAlias = $request->input('location_alias');
		if (!empty($locationAlias)){
			$parts = explode("\n", $locationAlias);
			foreach ($parts as $alias){
				if (empty($alias)){continue;}

				LocationAlias::create([
					'location_name' => trim($alias),
					'location_id' => $id,
					'order_number' => 99
				]);
			}
		}

		return redirect('admin/locations')->with('info', 'Item updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$location = Location::findOrFail($id);
		$location->delete();

		return redirect('admin/locations')->with('info', 'Item removed');
	}

}
