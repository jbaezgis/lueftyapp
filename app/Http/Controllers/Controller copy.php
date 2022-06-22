<?php namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Repositories\SiteRepository;
use Session;

abstract class Controller extends BaseController {

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public $selectedSite;
	public $currentSite;

	public function __construct(){
		$site = new SiteRepository;

		$this->selectedSite = $site->getSelectedSite();
		$this->currentSite = $site->getCurrentSite();
		
		config(['SelectedSite' => $this->selectedSite->id]);
		config(['SiteTheme' => $this->currentSite->theme]);
		config(['Site' => $this->currentSite]);

		view()->share('Sites', $site->getAll());

		$this->middleware(function ($request, $next) {
	 		if ($request->has('ref')) {
	 			$request->session()->put('searchengine', $request->get('ref'));
	 			$uri = $request->url();
	 			return redirect($uri);
        	}

        	\App::setLocale(session('language', 'en'));

        	return $next($request);
        });
	}
}
