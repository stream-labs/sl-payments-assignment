<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePricesRequest;
use App\Http\Requests\UpdatePricesRequest;
use App\Prices;

class PricesController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePricesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prices $prices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prices $prices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePricesRequest $request, Prices $prices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prices $prices)
    {
        //
    }
}
