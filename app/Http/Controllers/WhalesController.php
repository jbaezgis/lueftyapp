<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Tour;
use App\Order;

class WhalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('whales.index');
    }

    public function optionOne()
    {
        return view('whales.option_one');
    }

    public function optionTwo()
    {
        return view('whales.option_two');
    }

    public function optionThree()
    {
        return view('whales.option_three');
    }

    public function optionFour()
    {
        return view('whales.option_four');
    }

    public function optionGroupOne()
    {
        return view('whales.option_group_one');
    }

    public function optionGroupTwo()
    {
        return view('whales.option_group_two');
    }

    public function thanks()
    {
        return view('whales.thanks');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name'=>'required',
            'email'=> 'required',
            'phone' => 'required',
            'persons' => 'required',
            'date' => 'required',
            'hotel'=> 'required',
            'room_hotel'=> 'required',
        ]);

        $order = Order::create($request->all());
        $order->tour_id = 1;
        $order->total = 0.00;
        $order->save();
          
        return view('whales.thanks');
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
}
