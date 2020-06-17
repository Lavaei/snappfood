<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Http\Request;

class CallsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Call
     */
    public function store(Request $request)
    {
        return Call::create($request->all());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Call
     */
    public function update(Request $request, $id)
    {
        $call = Call::query()->find($id);

        if($call)
        {
            $call->update($request->all());
        }

        return $call;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return string
     */
    public function destroy($id)
    {
        Call::destroy($id);

        return "The operator has removed successfully.";
    }
}
