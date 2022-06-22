<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\LocationAlias;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Page;
use App\Repositories\PageRepository;
use DB;
use Mail;
use Route;

class PageController extends Controller {
	public $pageRepo;

	public function __construct(PageRepository $pageRepository){
		parent::__construct();
		$this->pageRepo = $pageRepository;
	}

	public function viewPage(){
		$page = $this->pageRepo->getRestPageBySlug(Route::currentRouteName());
		if (!$page) {
			abort(404);
		}

		return view('pages.generic_page', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords,
            'route' => Route::currentRouteName()
		]);
	}
	
	public function about()
	{
		return view('about-us');
	}

	public function PrivacyPolicy(){
		
		return view('privacy_policy', compact('privacy_policy'));
	}
	
	public function contacto(){
		
		return view('pages/contact', compact('contact'));
	}

	public function groundTransfer(Request $request)
	{
		$from = $request->get('from_location');
		$to = $request->get('to_location');

		if (!empty($from and $to)) {
			$from_location = LocationAlias::where('id', $from)->first();
			$to_location = LocationAlias::where('id', $to)->first();

			$findservice = Service::where('from', $from_location->location_id)->where('to', $to_location->location_id)->first();
			$serviceid = Service::find($findservice->id);

			return redirect('/request-ground-transfer-service?service=' . $serviceid->id . '&way=oneway&aFrom=0&aTo=0');
		}

			
				
		$data = $this->getLocationData('groundtransfer');
		$page = $this->pageRepo->getRestPageBySlug('ground-transfers');

		

		if (!$page) {
			abort(404);
		}
		
		return view('pages.groundtransfer', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		])->with(['servicesJson' => json_encode($data)]);
	}

	public function scheduledFlight()
	{
		$data = $this->getLocationData('scheduledflights');
		$page = $this->pageRepo->getRestPageBySlug('flight');

		if (!$page) {
			abort(404);
		}

		return view('pages.scheduledflight', [
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		])->with(['servicesJson' => json_encode($data)]);
	}

	public function charters()
	{
		$data = $this->getLocationData('charters');
		$page = $this->pageRepo->getRestPageBySlug('charter');

		if (!$page) {
			abort(404);
		}

		return view('pages.charters',[
			'page' => $page,
			'metaDescription' => $page->metaDescription,
			'metaKeywords' => $page->metaKeywords
		])->with(['servicesJson' => json_encode($data)]);
	}

	private function getLocationData($type){
		$data = new \stdClass();
		$data->$type = new \stdClass();
		$shortname = $type;

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
						DB::raw('services.id, services.from, 
							(SELECT location_name FROM locations WHERE locations.id=services.from) AS from_name,
							(SELECT order_number FROM locations WHERE locations.id=services.from) AS loc_order'))
						->where('services.service_type', $shortname)
						->where('services.site_id', $this->currentSite->service_instance)
						->orderBy('loc_order', 'ASC')
						->orderBy('from_name', 'ASC')
						->groupBy('services.from')
	                    ->get();
	    $data->$shortname->from = $From;

	    $From = Service::select(
						DB::raw('services.id, services.from, services.to,
							(SELECT location_name FROM locations WHERE locations.id=services.to) AS to_name,
							(SELECT order_number FROM locations WHERE locations.id=services.to) AS loc_order'))
						->where('services.service_type', $shortname)
						->where('services.site_id', $this->currentSite->service_instance)
						->orderBy('loc_order', 'ASC')	
						->orderBy('to_name', 'ASC')	
						->groupBy('services.to')
	                    ->get();
	    $data->$shortname->to = $From;
	}
	    return $data;
	}

	public function faqs(){
		$faqs = [
			'Currency & Money Exchange ( 3 )',
			'How will i find the Driver at the Airport? ( 2 )'
		];
		return view('pages.faqs', compact('faqs'));
	}

	public function faqsShow($id){
		if ($id == 1){
			$faqs = [
				[
					'title' => 'What is the local currency and the exchange rate in the Dominican Republic?',
					'text'	=> '&nbsp;&nbsp;<br />The official currency is the Dominican Peso (RD$) - the current exchange rate is approximately 1.00 U$ Dollar = 45.50 Dominican Pesos'
				],
				[
					'title' => 'Can i pay in U$ Dollars or Euros in the Dominican Republic?',
					'text'	=> '&nbsp;&nbsp;<br />Although the U$ Dollar is not an official currency in the Dominican Republic it is accepted almost everywhere, however you are much better off paying in Dominican Pesos.  Normally if you pay in U$ Dollars a much lower exchange rate will be applied than the rate you would get at an exchange facility or a bank.  The same is true for Euros'
				],
				[
					'title' => 'Where should i change U$ Dollars in Dominican Pesos?',
					'text'	=> '&nbsp;&nbsp;<br />You can change at Banks or Money Exchange facilities which you will see everywhere, especially in Tourist Areas.  The current exchange rate is approximately 1.00 U$ = 45.50 RD$.'
				]
			];
		} else {
					$faqs = [
				[
					'title' => 'Where will i find the Driver at the Airport after my arrival?',
					'text'	=> '&nbsp;&nbsp;<br />Just look out for a sign with YOUR NAME on it upon leaving the airport building.'
				],
				[
					'title' => 'How will I find the driver if I arrive late',
					'text'	=> '&nbsp;&nbsp;<br />Just give us a call anytime.'
				]
			];
		}
		return view('pages.faqsshow', compact('faqs'));
	}

	public function sendContactUs(Request $request){
		$rules = [
			'name'	=> 'required',
			'email'	=> 'required|email'
		];

		if (env('APP_ENV') == 'production') {
			$rules['g-recaptcha-response'] = 'required|captcha';
		}

		$this->validate($request, $rules);

		$name = $request->input("name");
		$sname = $request->input("second_name");
		$email = $request->input("email");
		$phone = $request->input('phone');
		$message = $request->input('message');

		$data = [
			'name' => $name,
			'sname' => $sname,
			'_email' => $email,
			'phone' => $phone,
			'_message' => $message
		];

		Mail::send('emails.contact', $data, function ($m) use ($data) {
	            $m->from('casedominicanshuttles@gmail.com', $this->currentSite->name);
	            // Notify the agent if necesary
	            if ($this->currentSite->is_agent == 1){
	                //$email = $m->to(\THESITE::get()->parentSite->email); // Main site email
	                
	                $email = $m->to('info@dominicanshuttles.com'); // Agent email
	                $email->bcc($data['_email']); // Agent email
	                $email->subject('New contact request'); 
	            } else {
	                $m->to('info@dominicanshuttles.com'); // Main site email
	                $m->bcc($data['_email']); // Agent email
	                $m->subject('New contact request'); 
	            }
	        
			});
			
		// Mail::send('emails.contact', $data, function ($m) use ($data) {
		// 	$m->from('info@'.$this->currentSite->domain, $this->currentSite->name);
		// 	// Notify the agent if necesary
		// 	if ($this->currentSite->is_agent == 1){
		// 		//$email = $m->to(\THESITE::get()->parentSite->email); // Main site email
				
		// 		$email = $m->to($this->currentSite->email); // Agent email
		// 		$email->bcc($data['_email']); // Agent email
		// 		$email->subject('New contact request'); 
		// 	} else {
		// 		$m->to($this->currentSite->email); // Main site email
		// 		$m->bcc($data['_email']); // Agent email
		// 		$m->subject('New contact request'); 
		// 	}
		
		// });

		return response()->json(['success' => 1]);
	}

	public function ecotours(){

		return view('pages.ecotours');
	}

	public function fourwheeler(){

		return view('pages.fourwheeler');
	}

	public function safaritruck(){

		return view('pages.safaritruck');
	}

	public function saonacatamaran(){

		return view('pages.saonacatamaran');
	}

	public function zipline(){

		return view('pages.zipline');
	}
}
