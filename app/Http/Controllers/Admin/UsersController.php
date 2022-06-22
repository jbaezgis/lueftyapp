<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Validator;

class UsersController extends Controller {

	function getIndex(){
		return view('admin.users.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function editAccount()
	{
		return view('auth.edit');
	}

	public function updateAccount(Request $request){
		$rules = [
			'name'		=> 'required|min:2|max:200',
			'email' => 'required|email|max:255|unique:users,email,'.Auth::user()->id
		];

		if ($request->has('password')){
			$rules['password'] = 'required|min:5';
			$rules['password2'] = 'required|min:5|same:password';
		}

		$this->validate($request, $rules);

		$user = Auth::user();
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		if ($request->has('password')){
			$user->password = bcrypt($request->input('password'));
		}

		$user->save();
		return redirect()->back()->with('info', 'Account updated');
	}
}
