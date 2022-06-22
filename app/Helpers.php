<?php
function booking_types(){
	return [
		'groundtransfer'	=> 'Ground Transfer'
	];
}

function booking_get_title($key){
	return booking_types()[$key];
}

function booking_price_options(){
	return [
		'1-5 passengers',
		'6-10 passengers'
	];
}

function formatDrivingTime($drivingTime){
	$parts = explode(":",$drivingTime);
	$output = "";
	if ($parts[0] != 0){
		$output .= "{$parts[0]} Hour ";
	}

	if (isset($parts[1]) && $parts[1] != 0){
		$output .= "{$parts[1]} Min ";
	}

	return $output;
}

function assetsUrl($path){
	return config('app.AssetUrls').'/'.$path;
}

function nl2p($string) {
	$string = nl2br($string);
	$output = preg_replace("/^(.*)<br.*\/?>/m", '<p>$1</p>', $string);
	return $output;
}

function setLog($type, $module, $action, $message = ''){
	$userId = 0;
	if (Auth::check()) {
		$userId = Auth::user()->id;
	}

	$log = App\Models\ActivityLog::create([
		'user_id' => $userId,
		'type' => $type,
		'module' => $module,
		'action' => $action,
		'message' => $message,
		'user_agent' => Request::server('HTTP_USER_AGENT'),
		'ip' => Request::ip()
	]);
}