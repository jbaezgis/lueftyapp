<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormsModel as FormModel;
use Session;
use View;

class FormsController extends Controller {

	public function __construct(){
		// Share data with all views
		//View::share('data', 'Test....');
	}

	public function getIndex(){
		$forms = FormModel::all();
		$trash_count = FormModel::onlyTrashed()->count();

		return view('admin.form.index', ['forms' 		=> $forms, 
										 'title' 		=> 'Forms', 
										 'is_trash' 	=> false,
										 'trashcount'	=> $trash_count]);
	}

	public function getTrash(){
		$forms = FormModel::onlyTrashed()->get();
		$trash_count = FormModel::onlyTrashed()->count();

		return view('admin.form.index', ['forms' => $forms, 
										 'title' => 'Trash', 
										 'is_trash' => true,
										 'trashcount'	=> $trash_count]);
	}

	public function getCreate()
	{
	 	return view('admin.form.create');
	}

	public function postCreate(Request $request){
		$form = new FormModel();
		
		$form->title = $request->input('title');
		$form->description = $request->input('description');

		$form->save();
		echo $form->id;

		Session::flash('info', 'Form created!');

		return redirect('admin/forms');
	}

	public function getDelete($id){
		$form  = FormModel::find($id);
		$form->delete();

		return redirect()->back()->with('info','Form moved to trash!');
	}

	public function getRestore($id){
		$form  = FormModel::onlyTrashed()->where('id', '=', $id)->firstOrFail();
		$form->restore();

		return redirect()->back()->with('info','Item restored !');
	}
}