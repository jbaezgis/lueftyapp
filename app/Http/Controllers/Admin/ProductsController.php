<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$products = Product::all();
		return view('admin.products.index', ['products'	=> $products]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.products.create', ['do'	=> 'create']);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		Product::create($request->all());

		return redirect('admin/products')->with('info', 'Product created successful!');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$product = Product::find($id);
		return view('admin.products.create', ['do'	=> 'update', 'product'	=> $product]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$product = Product::find($id);
		$product->name = $request->input('name');
		$product->price = $request->input('price');
		$product->content = $request->input('content');
		$product->remark = $request->input('remark');
		$product->code = $request->input('code');
		$product->save();

		return redirect('admin/products')->with('info', 'Product updated successful!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$product = Product::find($id);
		$product->delete();

		return redirect('admin/products')->with('info', 'Product removed successful!');
	}

}
