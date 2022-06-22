<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class LogController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$logs = ActivityLog::orderBy('created_at', 'DESC')->paginate(100);
		return view('admin.logs.index', ['logs'	=> $logs]);
	}
}