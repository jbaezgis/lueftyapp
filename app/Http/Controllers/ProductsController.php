<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Booking;

class ProductsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function askProducts(Request $request)
	{
		$bookingKey = $request->get('bookingkey');
		return view('products.askproduct', ['bookingkey' => $bookingKey]);

	}

	public function addProducts(Request $request){
		$bookingKey = $request->get('bookingkey');
		$booking = Booking::where('bookingKey', $bookingKey)->first();

		$currentProducts = $booking->products()->orderBy('booking_product.created_at', 'desc')->get();

		$total = $fee = config('app.cateringFee');
		foreach ($currentProducts as $currentProduct){
			$total += ($currentProduct->price * $currentProduct->pivot->qty);
		}

		$products = Product::all();
		return view('products.addproduct', [
			'products' 			=> $products, 
			'currentProducts'	=> $currentProducts,
			'total'				=> $total,
			'fee'				=> $fee,
			'bookingkey' 		=> $bookingKey
		]);
	}

	public function storeProduct(Request $request){
		$this->validate($request, [
			'product'	=> 'required|numeric',
			'qty'		=> 'required|numeric|min:1'
		]);

		$productId = $request->input('product');
		$bookingKey = $request->input('bookingkey');

		$booking = Booking::where('bookingkey', $bookingKey)->first();
		$booking->products()->attach($productId, [
			'qty'	=> $request->input('qty')
		]);

		return redirect('products/add?bookingkey='.$bookingKey.'#cart')->with('info_cart', 'Item added to cart!');
	}

	public function removeProduct(Request $request, $privotId){
		$bookingKey = $request->get('bookingkey');
		$booking = Booking::where('bookingKey', $bookingKey)->first();
		if ($booking){
			$pivot = \App\Models\BookingProduct::find($privotId);
			$pivot->delete();

			return redirect('products/add?bookingkey='.$bookingKey.'#cart')->with('info_cart', 'Item removed from cart!');
		}

		return redirect('products/add?bookingkey='.$bookingKey);
	}

	public function emptyCart(Request $request){
		$bookingKey = $request->get('bookingkey');
		$booking = Booking::where('bookingKey', $bookingKey)->firstOrFail();
		if ($booking){
			$booking->products()->detach();
			return redirect('products/add?bookingkey='.$bookingKey.'#cart')->with('info_cart', 'Cart empty!');
		}
	}

	public function cancelCatering(Request $request){
		$bookingKey = $request->get('bookingkey');
		$booking = Booking::where('bookingKey', $bookingKey)->firstOrFail();
		if ($booking){
			$booking->products()->detach();
			return redirect('confirm-transfer?bookingkey='.$bookingKey.'#cart')->with('info_cart', 'Cart empty!');
		}
	}
}
