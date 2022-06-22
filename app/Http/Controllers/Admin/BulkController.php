<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServicePrice;
use App\Models\Location;
use App\Models\ActivityLog;

class BulkController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$locations = Location::all();
		$logs = ActivityLog::where('module', 'BulkPrice')->orderBy('created_at', 'DESC')->paginate(10);

		$locationOptions = ['' => '-Select Location-'];
		foreach ($locations as $location){
			$locationOptions[$location->id]	= $location->location_name;
		}

		$typeOptions = ['' => '-Select Type-'];
		foreach(config('app.bookingTypes') as $shortname => $fullname){
			$typeOptions[$shortname]	= $fullname;
		}

		return view('admin.bulk.index', compact('locationOptions','typeOptions', 'logs'));
	}

	public function updateAll(Request $request){
		$this->validate($request, [
			'percent'	=> 'required|numeric|max:100|min:-80'
		]);

		$percent = $request->input('percent');
		
		$services = Service::where('site_id', $this->selectedSite->id)->get();
		$this->updatePrices($services, $percent);

		setLog('info', 'BulkPrice', 'Update', "Updated {$percent}% to all prices");

		return redirect()->back()->with('info', 'Prices updated');
	}

	public function updateByLocation(Request $request){
		$this->validate($request, [
			'percent'	=> 'required|numeric|max:100|min:-80',
			'route'	=> 'required',
			'location'	=> 'required'
		]);

		$way = $request->input("route");
		$percent = $request->input('percent');
		$location = $request->input('location');
		$services = Service::where($way, $location)
		                  ->where('site_id', $this->selectedSite->id)
		                  ->get();

		$this->updatePrices($services, $percent);

		$locationOb = Location::find($location);
		setLog('info', 'BulkPrice', 'Update', "Updated {$percent}% to all prices {$way} {$locationOb->location_name}");
		return redirect()->back()->with('info', 'Prices updated');
	}

	public function updateByType(Request $request){
		$this->validate($request, [
			'percent'	=> 'required|numeric|max:100|min:-80',
			'type'	=> 'required'
		]);

		$type = $request->input("type");
		$percent = $request->input('percent');
		$services = Service::where('service_type', $type)
						->where('site_id', $this->selectedSite->id)
		                 ->get();

		$this->updatePrices($services, $percent);

		setLog('info', 'BulkPrice', 'Update', "Updated {$percent}% to all prices of type {$type}");
		return redirect()->back()->with('info', 'Prices updated');
	}

	private function updatePrices($services, $variationAmount){
		foreach ($services as $service){
			foreach($service->prices as $price){
				$servicePrice = ServicePrice::find($price->id);
				$servicePrice->variation_type = 'percent';
				$servicePrice->variation_amount = $variationAmount;
				$servicePrice->save();
			}
		}
	}
}
