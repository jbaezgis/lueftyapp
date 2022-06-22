<?php namespace App\Repositories;

use App\Models\Site;
use Auth;
use App\User;
use Request;

class SiteRepository {
	public $selectedSite;

	public function getAll(){
		return Site::all();
	}

	public function getSelectedSite(){
		if(!Auth::guest()){
			$meta = Auth::user()->metas()->where('meta_name', 'selected_site')->first();
			$selectedSite = 0;

			if ($meta){
				$selectedSite = Site::find($meta->value);
			} else {
				$selectedSite = Site::take(1)->first();
			}

			return $selectedSite;
		} else {
			return Site::take(1)->first();
		}
	}

	/**
	 * Get the current site or load default if is local development env
	 */
	public function getCurrentSite(){
		if(config('app.env') == 'dev'){
			$site = Site::where('domain', env('SITE'))->firstOrFail();
			if ($site->is_agent == 0){
				$site->service_instance = $site->id;
			}
		} else {
			$wwwdomain = str_replace("www.", "", Request::getHost());
			$site = Site::where('domain', $wwwdomain)->first();
            
            // If no site is found then load the default
            if ( !$site ) {
                $site = Site::where('domain', env('SITE'))->firstOrFail();
            }

			if ( $site->is_agent == 0 ){
				$site->service_instance = $site->id;
			}
		}

		return $site;
	}
}
