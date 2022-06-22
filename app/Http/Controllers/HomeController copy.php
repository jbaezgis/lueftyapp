<?php namespace App\Http\Controllers;

use App\Models\Service;
use DB;
use Config;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Location;
use Cache;
use App\Repositories\PageRepository;
use Route;
use Session;

class HomeController extends Controller {

	public $pageRepo;

	public function __construct(PageRepository $pageRepository){
		parent::__construct();
		$this->pageRepo = $pageRepository;
		//$this->middleware('compress.html');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request, $account='main')
	{	
		$page = $this->pageRepo->getRestPageBySlug(Route::currentRouteName());

		$currentSiteServiceInstance = $this->currentSite->service_instance;

		$data = Cache::rememberForEver('locationsJSON', function() use ($currentSiteServiceInstance) {
			$data = new \stdClass();
			foreach (Config::get('app.bookingTypes') as $shortname => $fullname){
				$data->$shortname = new \stdClass();

				if ($shortname == 'groundtransfer'){
					$From = DB::select(DB::raw("
							SELECT l.location_name as from_name, l.id AS from_id, l.order_number FROM services s
							INNER JOIN locations l ON l.id=s.from
							WHERE s.service_type='groundtransfer'
							GROUP BY location_name
							UNION
							SELECT location_name as from_name, location_id AS from_id, order_number FROM location_alias
							ORDER BY order_number ASC, from_name ASC
						"));

					$data->$shortname->from = $From;

					$To = DB::select(DB::raw("
							SELECT l.location_name as to_name, l.id AS to_id, l.order_number FROM services s
							INNER JOIN locations l ON l.id=s.to
							WHERE s.service_type='groundtransfer'
							GROUP BY location_name
							UNION
							SELECT location_name as to_name, location_id AS to_id, order_number FROM location_alias
							ORDER BY order_number ASC, to_name ASC
						"));
					$data->$shortname->to = $To;

				} else {
					$From = Service::select(
							DB::raw('services.id, services.from as from_id, 
								(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
								(SELECT order_number FROM locations WHERE locations.id=services.from) AS loc_order')
							)
							->where('services.service_type', $shortname)
							->where('services.site_id', $currentSiteServiceInstance)
							->orderBy('loc_order', 'ASC')
							->orderBy('from_name', 'ASC')
							->groupBy('services.from')
		                    ->get();
					
				    $data->$shortname->from = $From;

				    $To =  Service::select(
									DB::raw('services.id, services.from as from_id, services.to as to_id, 
										(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name,
										(SELECT order_number FROM locations WHERE locations.id=services.to) AS loc_order'
										)
									)
									->where('services.service_type', $shortname)
									->where('services.site_id', $currentSiteServiceInstance)
									//->orderBy('services.airport_location_to', 'DESC')
									->orderBy('loc_order', 'ASC')	
									->orderBy('to_name', 'ASC')	
									->groupBy('services.to')
				                    ->get();

				    $data->$shortname->to = $To;
				}
			}

			return $data;
		});
		// $from_id = $request->get('from');
		// $to_id = $request->get('to');

		// if (!empty($from_id and $to_id)) {
		// 	// $from_location = LocationAlias::where('id', $from)->first();
		// 	// $to_location = LocationAlias::where('id', $to)->first();

		// 	$findservice = Service::where('from', $from->id)->where('to', $to->id)->first();
		// 	$serviceid = Service::find($findservice->id);

		// 	return redirect('/request-ground-transfer-service?service=' . $serviceid->id . '&way=oneway&aFrom=0&aTo=0');
		// }
		return view('home', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		])->with(['servicesJson' => json_encode($data)]);
	}

	public function toLocations($type, $from){
		$location = Location::where('location_name', $from)->first();
		if ($location){
			$locations = Service::where("from", $location->id)
						->where('service_type', $type)
						->groupBy('to')
		                ->get();

		    $output = [];
		    foreach ($locations as $location){
		    	$data = new \stdClass();
		    	$data->to = $location->to;
		    	$data->to_name = $location->tolocation->location_name;
		    	$output[] = $data; 
		    }
			return response()->json($output);
		}
	}

	public function newhome(Request $request, $account='main')
	{	
		$page = $this->pageRepo->getRestPageBySlug(Route::currentRouteName());

		$currentSiteServiceInstance = $this->currentSite->service_instance;

		$data = Cache::rememberForEver('locationsJSON', function() use ($currentSiteServiceInstance) {
			$data = new \stdClass();
			foreach (Config::get('app.bookingTypes') as $shortname => $fullname){
				$data->$shortname = new \stdClass();

				if ($shortname == 'groundtransfer'){
					$From = DB::select(DB::raw("
							SELECT l.location_name as from_name, l.id AS from_id, l.order_number FROM services s
							INNER JOIN locations l ON l.id=s.from
							WHERE s.service_type='groundtransfer'
							GROUP BY location_name
							UNION
							SELECT location_name as from_name, location_id AS from_id, order_number FROM location_alias
							ORDER BY order_number ASC, from_name ASC
						"));

					$data->$shortname->from = $From;

					$To = DB::select(DB::raw("
							SELECT l.location_name as to_name, l.id AS to_id, l.order_number FROM services s
							INNER JOIN locations l ON l.id=s.to
							WHERE s.service_type='groundtransfer'
							GROUP BY location_name
							UNION
							SELECT location_name as to_name, location_id AS to_id, order_number FROM location_alias
							ORDER BY order_number ASC, to_name ASC
						"));
					$data->$shortname->to = $To;

				} else {
					$From = Service::select(
							DB::raw('services.id, services.from as from_id, 
								(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
								(SELECT order_number FROM locations WHERE locations.id=services.from) AS loc_order')
							)
							->where('services.service_type', $shortname)
							->where('services.site_id', $currentSiteServiceInstance)
							->orderBy('loc_order', 'ASC')
							->orderBy('from_name', 'ASC')
							->groupBy('services.from')
							->get();
					
					$data->$shortname->from = $From;

					$To =  Service::select(
									DB::raw('services.id, services.from as from_id, services.to as to_id, 
										(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name,
										(SELECT order_number FROM locations WHERE locations.id=services.to) AS loc_order'
										)
									)
									->where('services.service_type', $shortname)
									->where('services.site_id', $currentSiteServiceInstance)
									//->orderBy('services.airport_location_to', 'DESC')
									->orderBy('loc_order', 'ASC')	
									->orderBy('to_name', 'ASC')	
									->groupBy('services.to')
									->get();

					$data->$shortname->to = $To;
				}
			}

			return $data;
		});

		

		return view('home', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		])->with(['servicesJson' => json_encode($data)]);
	}
}