<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\PriceOption;
use App\Models\Service;
use App\Models\ServicePrice;
use App\Models\FerryShuttleHotel;
use App\Repositories\SiteRepository;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Http\JsonResponse;
use DB;
use Config;

class ServicesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, $type = 'groundtransfer')
	{
		$query = $this->selectedSite->services()->where('service_type', $type);
		if ($request->has('from')){
			$from = $request->get('from');
			$query->whereRaw("(SELECT location_name FROM locations WHERE id=services.from) LIKE '%{$from}%'");
		}

		if ($request->has('to')){
			$to = $request->get('to');
			$query->whereRaw("(SELECT location_name FROM locations WHERE id=services.to) LIKE '%{$to}%'");
		}

		$services = $query->orderBy('from', 'ASC')->paginate(50);

		$priceOptions = ServicePrice::get();
		
		return view('admin.services.index',
			[
				'type'	=> $type,
				'services'	=> $services,
				'from' => $request->get('from'),
				'to' => $request->get('to'),
				'priceOptions' => $priceOptions,
				'domain' => $this->selectedSite->domain
			]);
	}

	public function servicesList(Request $request)
	{

		// $query = $this->selectedSite->services()->where('service_type', $type);

		$from = $request->get('from');
		$to = $request->get('to');

		if ($request->has('from') and $request->has('to')){
			// $services->whereRaw("(SELECT location_name FROM locations WHERE id=services.from) LIKE '%{$from}%'");
			$services = Service::where('from', $from)->where('to', $to)->paginate(50);
		}elseif($request->has('from')){
			// $services->whereRaw("(SELECT location_name FROM locations WHERE id=services.to) LIKE '%{$to}%'");
			$services = Service::where('from', $from)->paginate(50);
		}elseif($request->has('to')){
			$to = $request->get('to');
			// $services->whereRaw("(SELECT location_name FROM locations WHERE id=services.to) LIKE '%{$to}%'");
			$services = Service::where('to', $to)->paginate(50);
		}else{
			$services = Service::get();
			// $services = Service::select('from', 'to')
			// ->groupBy('from', 'to')
			// ->havingRaw('COUNT(*) > 1')
			// ->get();
		}

		// $services = $query->orderBy('from', 'ASC')->paginate(50);
		$priceOptions = ServicePrice::get();

		return view('admin.services.services_list', compact('services', 'priceOptions', 'from', 'to'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($type = 'groundtransfer')
	{
		$data = [
			'do'	=> 'create',
			'locations'	=> Location::all(),
			'priceOptions'	=> PriceOption::all(),
			'type'	=> $type,
			'title'	=>  Config::get('app.bookingTypes.'.$type)
		];

		$view = 'create';
		if ($type == 'ferryshuttle'){
			$view = 'create_ferryshuttle';
		} else if($type == 'scheduledflights'){
			$view = 'create_scheduledflights';
		} else if($type == 'charters'){
			$data['airplains'] = \App\Models\Airplain::all();
			$view = 'create_charters';
		}

		return view('admin.services.'.$view, $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{	
		$do = 'create';
		$id = null;

		$validator = Validator::make($request->all(), [
            'from'			=> 'required',
			'to'			=> 'required|different:from',
			'driving_time'	=> 'required',
			'map_image'		=> 'image'
        ]);

        $validator->after(function($validator) use($request, $id) {
		    if ($this->checkIfServiceExists($request, $id)) {
		        $validator->errors()->add('to', 'There is a service already created for these locations');
		    }
		});

        if ($validator->fails()) {
            return new JsonResponse($validator->messages(), 422);
        }

        if ($request->has('map_image')){
        	$image = Input::file('image');
	        $filename  = time() . '.' . $image->getClientOriginalExtension();

	        $path = public_path('maps/' . $filename);

	        Image::make($image->getRealPath())->resize(200, 200)->save($path);
	        $user->image = $filename;
	        $user->save();
        }
        

		// Save service
		$service = Service::create([
			'from' 			=> $request->input('from'),
			'to' 			=> $request->input('to'),
			'service_type' 	=> $request->input('service_type'),
			'driving_time' 	=> $request->input('driving_time'),
			'mapimage' 		=> $request->input('mapimage'),
			'site_id'		=> $this->selectedSite->id
		]); 

		$this->savePriceOptions($request, $service);

		$request->session()->flash('info', 'Service created successful!');

		return 'success';
	}

	public function storeFerry(Request $request){
		$do = 'create';
		$id = null;

		$validator = Validator::make($request->all(), [
            'from'			=> 'required',
			'to'			=> 'required|different:from',
			'price_first_person'			=> 'required|numeric',
			'price_aditional_person'		=> 'required|numeric'
        ]);

        $validator->after(function($validator) use($request, $id) {
		    if ($this->checkIfServiceExists($request, $id)) {
		        $validator->errors()->add('to', 'There is a service already created for these locations');
		    }
		});

		if ($validator->fails()) {
            return new JsonResponse($validator->messages(), 422);
        }

        // Save service
		$service = Service::create([
			'from' 			=> $request->input('from'),
			'to' 			=> $request->input('to'),
			'service_type' 	=> $request->input('service_type'),
			'price_first_person'	=> $request->input('price_first_person'),
			'price_aditional_person'	=> $request->input('price_aditional_person'),
			'site_id'		=> $this->selectedSite->id
		]); 

		$this->saveHotels($request, $service);
		$this->savePriceOptions($request, $service);

		$request->session()->flash('info', 'Service created successful!');

		return 'success';
	}

	public function storescheduleflights(Request $request){
		$do = 'create';
		$id = null;

		$validator = Validator::make($request->all(), [
            'from'			=> 'required',
			'to'			=> 'required|different:from'
        ]);

  //       $validator->after(function($validator) use($request, $id) {
		//     if ($this->checkIfServiceExists($request, $id)) {
		//         $validator->errors()->add('to', 'There is a service already created for these locations');
		//     }
		// });

		if ($validator->fails()) {
            return new JsonResponse($validator->messages(), 422);
        }

        // Save service
		$service = Service::create([
			'from' 			=> $request->input('from'),
			'to' 			=> $request->input('to'),
			'service_type' 	=> $request->input('service_type'),
			'driving_time' 	=> $request->input('driving_time'),
			'arrival_time' 	=> $request->input('arrival_time'),
			'departure_time_return' 	=> $request->input('departure_time_return'),
			'arrival_time_return' 	=> $request->input('arrival_time_return'),
			'price_per_children' 	=> $request->input('price_per_children'),
			'site_id'		=> $this->selectedSite->id
		]); 

		FerryShuttleHotel::where('service_id', $service->id)->delete();

		$this->saveAditionalShuttle($request, $service, 'from');
		$this->saveAditionalShuttle($request, $service, 'to');
		$this->savePriceOptions($request, $service);

		$request->session()->flash('info', 'Service created successful!');

		return 'success';
	}

	public function storecharter(Request $request){
		$do = 'create';
		$id = null;

		$validator = Validator::make($request->all(), [
            'from'			=> 'required',
			'to'			=> 'required|different:from'
        ]);

        $validator->after(function($validator) use($request, $id) {
		    if ($this->checkIfServiceExists($request, $id)) {
		        $validator->errors()->add('to', 'There is a service already created for these locations');
		    }
		});

		if ($validator->fails()) {
            return new JsonResponse($validator->messages(), 422);
        }

        // Save service
		$service = Service::create([
			'from' 			=> $request->input('from'),
			'to' 			=> $request->input('to'),
			'service_type' 	=> $request->input('service_type'),
			'site_id'		=> $this->selectedSite->id,
			'mapimage' 		=> $request->input('mapimage')
		]);

		$this->saveAditionalShuttle($request, $service, 'from');
		$this->saveAditionalShuttle($request, $service, 'to');
		$this->saveAirplainOptions($request, $service);

		$request->session()->flash('info', 'Service created successful!');

		return 'success';
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$service = Service::findOrFail($id);
		$priceOptions = ServicePrice::where('service_id', $id)->get();

		//return '<pre>'.print_r($priceOptions, true).'</pre>';

		$data = [
			'do'	=> 'edit',
			'service'	=> $service,
			'prices'	=> $priceOptions,
			'locations'	=> Location::all(),
			'priceOptions'	=> PriceOption::all(),
			'type'	=> $service->service_type,
			'title'	=>  Config::get('app.bookingTypes.'.$service->service_type)
		];

		$view = 'create';
		if ($service->service_type == 'ferryshuttle'){
			$view = 'create_ferryshuttle';
		} else if($service->service_type == 'scheduledflights'){
			$view = 'create_scheduledflights';
		} else if($service->service_type == 'charters'){
			$data['airplains'] = \App\Models\Airplain::all();
			$data['prices'] = \App\Models\AirplainPrice::where('service_id', $id)->get();
			$view = 'create_charters';
		}

		return view('admin.services.'.$view, $data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$do = 'update';

		$validator = Validator::make($request->all(), [
			'driving_time'	=> 'required',
			'map_image'		=> 'image'
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->messages(), 422);
        }

        $service = Service::find($id);
        $service->service_type = $request->input('service_type');
        $service->driving_time = $request->input('driving_time');
        $service->mapimage = $request->input('mapimage');
        $service->save();

        ServicePrice::where('service_id', $id)->delete();

		$this->savePriceOptions($request, $service);

		$request->session()->flash('info', 'Service updated successful!');

		return 'success';
	}

	public function updateferry(Request $request, $id)
	{
		$do = 'update';

		$validator = Validator::make($request->all(), [
            'price_first_person'			=> 'required|numeric',
			'price_aditional_person'		=> 'required|numeric'
        ]);

		if ($validator->fails()) {
            return new JsonResponse($validator->messages(), 422);
        }

        $service = Service::find($id);
        $service->service_type = $request->input('service_type');
        $service->price_first_person = $request->input('price_first_person');
        $service->price_aditional_person = $request->input('price_aditional_person');
        $service->save();

        ServicePrice::where('service_id', $id)->delete();

		$this->saveHotels($request, $service);
		$this->savePriceOptions($request, $service);

		$request->session()->flash('info', 'Service updated successful!');

		return 'success';
	}

	public function updatescheduleflights(Request $request, $id)
	{
		$do = 'update';

        $service = Service::find($id);
        $service->driving_time = $request->input('driving_time');
        $service->arrival_time = $request->input('arrival_time');
        $service->departure_time_return = $request->input('departure_time_return');
        $service->arrival_time_return = $request->input('arrival_time_return');
        $service->price_per_children = $request->input('price_per_children');
        $service->mapimage		= $request->input('mapimage');
        $service->save();

        ServicePrice::where('service_id', $id)->delete();

		FerryShuttleHotel::where('service_id', $service->id)->delete();

		$this->saveAditionalShuttle($request, $service, 'from');
		$this->saveAditionalShuttle($request, $service, 'to');
		$this->savePriceOptions($request, $service);

		$request->session()->flash('info', 'Service updated successful!');

		return 'success';
	}

	public function updatecharter(Request $request, $id){
		$do = 'update';

        $service = Service::find($id);
        $service->mapimage		= $request->input('mapimage');
        $service->update();

        \App\Models\AirplainPrice::where('service_id', $id)->delete();
        FerryShuttleHotel::where('service_id', $service->id)->delete();

		$this->saveAditionalShuttle($request, $service, 'from');
		$this->saveAditionalShuttle($request, $service, 'to');
		$this->saveAirplainOptions($request, $service);

		$request->session()->flash('info', 'Service updated successful!');

		return 'success';
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Service::where('id', $id)->delete();
		ServicePrice::where('service_id', $id)->delete();
		//$request->session()->flash('info', 'Service removed successful!');
		return redirect()->back()->with('info', 'Service removed successful!');
	}

	public function savePriceOptions($request, $service){
		$priceOption = $request->input('price_option');
		$priceOneWay = $request->input('price_oneway');
		$priceRoundTrip = $request->input('price_roundtrip');

		if (is_array($priceOption) && count($priceOption) > 0) {
			$servicePricesObject = [];

			for ($i = 0; $i < count($priceOption); $i++) {
				if (empty($priceOption) && empty($priceOneWay) && empty($priceRoundTrip)) {
					// All is empty, do not save it
				} else {
					$servicePricesObject[] = new ServicePrice([
						'price_option_id'	=> $priceOption[$i],
						'oneway_price'		=> $priceOneWay[$i],
						'roundtrip_price' 	=> $priceRoundTrip[$i]
					]);
				}
			}

			if (count($servicePricesObject) > 0) {
				$service->prices()->saveMany($servicePricesObject);
			}
		}
	}

	public function saveAirplainOptions($request, $service){
		$priceOption = $request->input('price_option');
		$price = $request->input('price');
		$time = $request->input('time');

		if (is_array($priceOption) && count($priceOption) > 0) {
			$servicePricesObject = [];

			for ($i = 0; $i < count($priceOption); $i++) {
				if (empty($priceOption) && empty($price)) {
					// All is empty, do not save it
				} else {
					$servicePricesObject[] = new \App\Models\AirplainPrice([
						'airplain_id'	=> $priceOption[$i],
						'price'		=> $price[$i],
						'driving_time' => $time[$i]
					]);
				}
			}

			if (count($servicePricesObject) > 0) {
				$service->airplainPrices()->saveMany($servicePricesObject);
			}
		}
	}

	public function saveHotels($request, $service){
		FerryShuttleHotel::where('service_id', $service->id)->delete();

		$hotel = $request->input('hotel');
		$pickup_time = $request->input('pickup_time');
		$price = $request->input('price');
		$note = $request->input('note');

		if (is_array($hotel) && count($hotel) > 0) {
			for ($i = 0; $i < count($hotel); $i++) {
				if (empty($hotel)) {
					
				} else {
					FerryShuttleHotel::create([
						'service_id'		=> $service->id,
						'hotel'				=> $hotel[$i],
						'pickup_time'		=> $pickup_time[$i],
						'price'				=> $price[$i],
						'note'				=> $note[$i]
					]);
				}
			}
		}
	}

	public function saveAditionalShuttle($request, $service, $key){
		$from_destination = $request->input($key.'_destination');
		$pickup_time = $request->input($key.'_pickuptime');
		$price = $request->input($key.'_price');
		$note = $request->input($key.'_shuttleduration');

		if (is_array($from_destination) && count($from_destination) > 0) {
			for ($i = 0; $i < count($from_destination); $i++) {
				if (empty($from_destination[$i]) && empty($pickup_time[$i]) && empty($price[$i])) {
					
				} else {
					FerryShuttleHotel::create([
						'service_id'		=> $service->id,
						'hotel'				=> $from_destination[$i],
						'pickup_time'		=> $pickup_time[$i],
						'price'				=> $price[$i],
						'note'				=> $note[$i],
						'way'				=> $key
					]);
				}
			}
		}
	}

	public function checkIfServiceExists($request){
		$from = $request->input('from');
		$to = $request->input('to');
		$serviceType = $request->input('service_type');

			$serviceExists = Service::where('from', $from)
			                        ->where('to', $to)
			                        ->where('service_type', $serviceType)
			                       	->where('site_id', $this->selectedSite->id)
			                        ->exists();

		return $serviceExists;
	}

	public function getUploadimage(){
		return view('admin.services.uploadimage', ['do'	=> 'upload']);
	}

	public function postUploadimage(Request $request){
		$this->validate($request, [
			'image'	=> 'required|image'
		]);

		if ($request->has('oldimage')){
			$oldimage = '/home/privauk0/public_html/maps/'.$request->input('oldimage');
			@unlink($oldimage);
		}

		$image = $request->file('image');
		$filename = time().$image->getClientOriginalName();
		
		$path = '/home/privauk0/public_html/maps/';
		$image->move($path, $filename);

		return view('admin.services.uploadimage', ['do'	=> 'setImage', 'filename' => $filename]);
	}

	public function getRemoveimage($imagepath){
		$oldimage = public_path('maps/'.$imagepath);
		@unlink($oldimage);
	}

	public function getBulk($locationId){
		$type = 'groundtransfer';
		$location  = Location::findOrFail($locationId);

		$data = [
			'do'	=> 'create',
			'locations'	=> Location::all(),
			'priceOptions'	=> PriceOption::all(),
			'type'	=> $type,
			'title'	=>  Config::get('app.bookingTypes.'.$type),
			'location' => $location
		];

		return view('admin.services.bulk', $data);
	}
}