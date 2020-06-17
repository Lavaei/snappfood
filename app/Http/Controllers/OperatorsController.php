<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OperatorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index()
    {
        return Operator::getAll();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Operator
     */
    public function store(Request $request)
    {
        return Operator::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Operator
     */
    public function show($id)
    {
        return Operator::getByID($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Operator
     */
    public function update(Request $request, $id)
    {
        $operator = Operator::getByID($id);

        if($operator)
        {
            $operator->update($request->all());
        }

        return $operator;
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
        Operator::destroy($id);

        return "The operator has removed successfully.";
    }

    /**
     * Assign a low priority call if any exist and  return it
     *
     * @param $operatorID
     *
     * @return Call
     */
    public function pick($operatorID)
    {
        $operator = Operator::getByID($operatorID);
        $call = Call::getFirstLowPriorityCall();

        if(!$operator)
        {
            return response()->json(
                "The operator does not exist!",
                422
            );
        }

        if($operator->isBusy())
        {
            return response()->json(
                "The operator is busy, please hang up the phone first!",
                422
            );
        }

        if(!$call)
        {
            return response()->json(
                "There is no low priority call!",
                422
            );
        }


        $call->assign((int)$operatorID);

        return $call;
    }
}
