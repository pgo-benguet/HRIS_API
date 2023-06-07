<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHolidayRequest;
use App\Http\Resources\HolidayResource;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;

class HolidaysController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HolidayResource::collection(
            Holiday::all()
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHolidayRequest $request)
    {
        // validate input fields
        $request->validated($request->all());

        // validate user from database
        $holiday_exist = Holiday::where([['title', $request->title], ['date', Date('Y-m-d', strtotime($request->date))]])->exists();
        if ($holiday_exist) {
            return $this->error('', 'Duplicate Entry', 400);
        }

        Holiday::create([
            "title" => $request->title,
            "date" => Date('Y-m-d', strtotime($request->date))
        ]);


        // return message
        return $this->success('', 'Successfully Saved', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Holiday $holiday)
    {
        return new HolidayResource($holiday);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreHolidayRequest $request, Holiday $holiday)
    {
        $holiday->date = Date('Y-m-d', strtotime($request->date));
        $holiday->title = $request->title;
        $holiday->save();

        // $holiday->update($request->all());
        return new HolidayResource($holiday);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
            $holiday->delete();
            return $this->success('', 'Successfully Deleted', 200);
    }
    public function search(Request $request)
    {  
        $activePage = $request->activePage;
        $searchKeyword = $request->searchKeyword;
        $orderAscending = $request->orderAscending;
        $orderBy = $request->orderBy;
        $orderAscending  ? $orderAscending = "asc" : $orderAscending = "desc";
        $searchKeyword == null ? $searchKeyword = "" : $searchKeyword = $searchKeyword;
        $orderBy == null ? $orderBy = "id" : $orderBy = $orderBy;

        $data = HolidayResource::collection(
            Holiday::where("id", "like", "%" . $searchKeyword . "%")
                ->orWhere("title", "like", "%" . $searchKeyword . "%")
                ->orWhere("date", "like", "%" . $searchKeyword . "%")
                ->skip(($activePage - 1) * 10)
                ->orderBy($orderBy, $orderAscending)
                ->take(10)
                ->get()
        );
        $pages = Holiday::where("id", "like", "%" . $searchKeyword . "%")
            ->orWhere("title", "like", "%" . $searchKeyword . "%")
            ->orWhere("date", "like", "%" . $searchKeyword . "%") 
            ->orderBy($orderBy, $orderAscending)
            ->count();

        return compact('pages', 'data');
    }
}

