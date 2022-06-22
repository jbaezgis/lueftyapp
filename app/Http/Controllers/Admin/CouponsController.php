<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$coupons = Coupon::orderBy('created_at', 'DESC')->get();
		return view('admin.coupons.index', ['coupons'	=> $coupons]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.coupons.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'code'		=> 'required|min:2|max:50',
			'discount'		=> 'required|numeric',
			'expiration_date' => 'date'
		]);

		$coupon = Coupon::create($request->all());

		return redirect('admin/coupons')->with('info', 'Coupon created successful!');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$coupon = Coupon::find($id);
		return view('admin.coupons.edit', compact('coupon'));
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
			'code'		=> 'required|min:2|max:50',
			'discount'		=> 'required|numeric',
			'expiration_date' => 'date'
		]);

		$coupon = Coupon::find($id);
		$coupon->update($request->all());

		return redirect('admin/coupons')->with('info', 'Coupon updated successful!');
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$coupon = Coupon::find($id);
		$coupon->delete();

		return redirect('admin/coupons')->with('info', 'Coupon removed successful!');
	}

}
