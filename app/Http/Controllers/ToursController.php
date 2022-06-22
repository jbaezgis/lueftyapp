<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Tour;
use App\Order;

class ToursController extends Controller {

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Tour $tours)
	{
        $perPage = 15;

        $orders = Order::latest()->paginate($perPage);

		return view('admin.tours.index', compact('orders'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tours.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'=>'required',
            'name'=> 'required',
            'slug' => 'required',
            'description' => 'required',
            'location' => 'required',
            'price'=> 'required|integer',
        ]);

        // $request->validate([
        //     'title'=>'required',
        //     'name'=> 'required',
        //     'slug' => 'required',
        //     'description' => 'required',
        //     'location' => 'required',
        //     'price'=> 'required|integer',
        //   ]);

          Tour::create($request->all());
        //   $share->save();
          
        return view('admin.tours.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
      
    public function from_punta_cana()
	{
        return view('tours.from_punta_cana');
    }

    public function from_puerto_plata()
	{
        return view('tours.from_puerto_plata');
    }

    public function from_la_romana()
	{
        return view('tours.from_la_romana');
    }

    public function from_samana()
	{
        return view('tours.from_samana');
    }

    public function from_boca_chica()
	{
        return view('tours.from_boca_chica');
    }

    public function from_santo_domingo_city()
	{
        return view('tours.from_santo_domingo_city');
    }

    public function from_jarabacoa()
	{
        return view('tours.from_jarabacoa');
    }

    public function from_juan_dolio()
	{
        return view('tours.from_juan_dolio');
    }

    public function from_santiago()
	{
        return view('tours.from_santiago');
    }


}
