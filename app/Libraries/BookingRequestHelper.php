<?php namespace App\Libraries;
use Cache;
use App\Models\LocationAlias;

class BookingRequestHelper
{
	public $request;
	public $repoLocation;

	public function init($request = null, $repoLocation = null){
		$this->request = $request;
		$this->repoLocation = $repoLocation;
	}

	public function ResolveLocation($repoLocation, $route, $alias = false){
		$output = new \StdClass;

		$parts = explode("-to-", $route);
		$fromString = urldecode(trim($parts[0]));
		$toString = urldecode(trim($parts[1]));
		$from = 0;
		$to = 0;
		$aliasFromID = 0;
		$aliasToID = 0;

		// Check if has alias
		$locationFrom = $repoLocation->getByName($fromString);
		if ($locationFrom){
			$from = $locationFrom->id;
			if ($alias) {
				$locationFrom = LocationAlias::where('location_name', $fromString)->first();
				if ($locationFrom){
					$from = $locationFrom->location_id;
					$aliasFromID = $locationFrom->id;
				}
			}
		} else {
			$locationFrom = LocationAlias::where('location_name', $fromString)->first();
			if ($locationFrom){
				$from = $locationFrom->location_id;
				$aliasFromID = $locationFrom->id;
			}
		}

		$locationTo = $repoLocation->getByName($toString);
		if ($locationTo){
			$to = $locationTo->id;
			if ($alias) {
				$locationTo = LocationAlias::where('location_name', $toString)->first();
			if ($locationTo){
					$to = $locationTo->location_id;
					$aliasToID = $locationTo->id;
				}
			}
		} else {
			$locationTo = LocationAlias::where('location_name', $toString)->first();
			if ($locationTo){
				$to = $locationTo->location_id;
				$aliasToID = $locationTo->id;
			}
		}

		$output->from = $from;
		$output->to = $to;
		$output->aliasFromID = $aliasFromID;
		$output->aliasToID = $aliasToID;
		$output->fromStr = $fromString;
		$output->toStr = $toString;

		return $output;
	}

	public function SaveMeta($booking, $metaName, $value){
		if ($this->request->has($metaName)){
			$booking->metas()->save( new BookingMeta([
				'meta_name' => $metaName, 
				'value' => $value
			]));
		}
	}

	public function getHours(){
		$output = Cache::rememberForEver('hoursList', function(){
			$output = [];
			$h = 0;
			$h24 = 0;
			$m = 15;
			$limit = (23 * 4) + 3;
			$meridian = 'AM';
			$canchangemeridiam = true;

			for ($i = 0; $i <= $limit; $i++) {
				$zero = $h < 10 ? '0' : '';
				$zeroa = $m < 10 ? '0' : '';
				$zeroh24 = $h24 < 10 ? '0' : '';

				$output[] = "{$zero}{$h}:{$zeroa}{$m} $meridian ({$zeroh24}{$h24}:{$zeroa}{$m} H)";
				if ($m == 45) {
					$m = 0;
					$h++;
					$h24++;
				} else {
					$m += 15;
				}

				if ($h >= 12){
					if ($canchangemeridiam) {
						$meridian = $meridian == 'PM' ? 'AM' : 'PM';
						$canchangemeridiam = false;
					}
				}

				if ($h > 12){
					$h = 1;
					$canchangemeridiam = true;

				}
			}

			return $output;
		});
		

		return $output;
	}
}
?>