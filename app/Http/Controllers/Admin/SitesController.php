<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Site;
use Session;
use Auth;
use App\User;
use App\Repositories\SiteRepository;

class SitesController extends Controller {
	public $site;

	public function __construct(SiteRepository $site){
		parent::__construct();

		$this->site = $site;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sites = site::all();
		return view('admin.sites.index', ['sites'	=> $sites]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$options = ['' => '-Select Site-'];
		foreach($this->site->getAll() as $site){
			$options[$site->id] = $site->domain;
		}
		return view('admin.sites.create', compact('options'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'name'		=> 'required|min:2|max:200',
			'domain'	=> 'required',
			'email'		=> 'required|email',
			'phone'		=> 'required',
			'theme'		=> 'required',
			'logo'		=> 'required',
		]);

		$site = Site::create($request->all());

		$image 		= $request->file('logo');
		$filename 	= time().$image->getClientOriginalName();
		$path 	= 	public_path('logos/');
		$image->move($path, $filename);

		$image 		= $request->file('favicon');
		$faviconfilename 	= time().$image->getClientOriginalName();
		$path 	= 	public_path('logos/');
		$image->move($path, $faviconfilename);

		$site->logo = $filename;
		$site->favicon = $faviconfilename;
		$site->save();

		return redirect('admin/sites')->with('info', 'Site created successful!');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$options = ['' => '-Select Site-'];
		foreach($this->site->getAll() as $site){
			$options[$site->id] = $site->domain;
		}

		$site = Site::find($id);
		$tab = 'site';
		return view('admin.sites.edit', compact('site', 'options', 'tab'));
	}

	public function editagent($id){
		$options = ['' => '-Select Site-'];
		foreach($this->site->getAll() as $site){
			$options[$site->id] = $site->domain;
		}

		$site = Site::find($id);
		$tab = 'agent';

		$user = User::firstOrCreate(['site_id' => $site->id, 'role' => 'agent']);

		return view('admin.sites.edit_agent', compact('site', 'options', 'tab', 'user'));
	}

	public function editmetas($id){
		$site = Site::find($id);
		$tab = 'metas';
		return view('admin.sites.edit_metas', compact('tab', 'site'));
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
			'name'		=> 'required|min:2|max:200',
			'domain'	=> 'required',
			'email'		=> 'required|email',
			'phone'		=> 'required',
			'theme'		=> 'required',
		]);

		$site = Site::find($id);
		$input = $request->all();
		$input['is_agent'] = $request->has('is_agent') ? 1 : 0;
		$site->update($input);

		if ($request->hasFile('logo')){
			$oldimage = public_path('logos/'.$site->logo);
			@unlink($oldimage);

			$image 		= $request->file('logo');
			$filename 	= time().$image->getClientOriginalName();
			$path 	= 	'logos/';
			$image->move($path, $filename);

			$site->logo = $filename;
			$site->save();
		}

		if ($request->hasFile('favicon')){
			$image 		= $request->file('favicon');
			$faviconFilename 	= time().$image->getClientOriginalName();
			$path 	= 	'logos/';
			$image->move($path, $faviconFilename);

			$site->favicon = $faviconFilename;
			$site->save();
		}

		return redirect('admin/sites')->with('info', 'site updated successful!');
	}

	public function updateagent(Request $request, $id){
		$this->validate($request, [
			'name'		=> 'required|min:2|max:200',
			'email' => 'required|email|max:255|unique:users,email,'.$id
		]);

		$user = User::find($id);
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		if ($request->has('password')){
			$user->password = bcrypt($request->input('password'));
		}

		$user->save();

		if ($request->has('percent')){
			$site = Site::find($request->input('site_id'));
			$site->percent = $request->input('percent');
			$site->save();
		}

		return redirect()->back()->with('info', 'Agent updated');
	}

	public function updatemetas(Request $request, $siteId){
		$site = Site::find($siteId);
		$site->metas = $request->input('metas');
		$site->save();

		return redirect()->back()->with('info', 'Metas updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$site = Site::find($id);
		$site->delete();

		return redirect('admin/sites')->with('info', 'site removed successful!');
	}

	public function switchsite(Request $request){
		$siteId = $request->get('site_id');

		$meta = Auth::user()->metas()->where('meta_name', 'selected_site')->first();
		if ($meta){
			$meta->update([
				'meta_name' => 'selected_site',
				'value'	=> $siteId
			]);
		} else {
			\App\Models\UserMeta::create([
				'user_id' => Auth::user()->id,
				'meta_name' => 'selected_site',
				'value'	=> $siteId
			]);
		}

		return redirect()->back();
	}

}
