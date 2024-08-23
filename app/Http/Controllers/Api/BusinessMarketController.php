<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessMarket;
use App\Models\BusinessMarketFacebook;
use App\Models\BusinessMarketHistory;
use App\Models\BusinessMarketVolume;
use App\Models\Campuses;
use Illuminate\Http\Request;

class BusinessMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $markets = BusinessMarket::with(['campuses', 'volume', 'facebook', 'history', 'cities', 'districts'])->orderBy('id', 'DESC')->paginate(15);

        if (!$markets) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $middleSchoolCount = 0;
        $primarySchoolCount = 0;
        $highSchoolCount = 0;
        $collegeCount = 0;
        $workingCount = 0;
        foreach ($markets as $item) {
            $campuses = json_decode($item->campuses_id);
            $temp = [];
        
            foreach ($campuses as $campus) {
                $campusTitle = Campuses::where('code', $campus)->first()->title ?? 'Unknown';
                $temp[] = $campusTitle;
            }
        
            switch ($item->segment) {
                case 1:
                    $primarySchoolCount++;
                    break;
                case 2:
                    $middleSchoolCount++;
                    break;
                case 3:
                    $highSchoolCount++;
                    break;
                case 4:
                    $collegeCount++;
                    break;
                case 5:
                    $workingCount++;
                    break;
            }
        
            $item->list_campus = implode(', ', $temp);
        }

        return response()->json([
            'data' => $markets,
            'statistic' => [
                'middleSchoolCount' => $middleSchoolCount,
                'primarySchoolCount' => $primarySchoolCount,
                'highSchoolCount' => $highSchoolCount,
                'collegeCount' => $collegeCount,
                'workingCount' => $workingCount,
            ]
        ]);
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
        $array = $request->all();
        $market = new BusinessMarket;
        $market->title = $array['title'];
        $market->segment = $array['segment'];
        $market->link_map = $array['link_map'] ?? '';
        $market->city_id = $array['city_id'];
        $market->district_id = $array['district_id'];
        $market->potential = $array['potential'];
        $market->note = $array['note'] ?? '';
        $market->active = 1;
        $market->campuses_id = json_encode($array['campuses']);
        $market->total_student = $array['total_student'];

        $market->save();

        if ($market->id) {
            $market_volume = new BusinessMarketVolume;
            $market_volume->market_id = $market->id;
            $market_volume->year = $array['year']['value'] ?? 0;
            $market_volume->more_level = json_encode($array['volume']);
            $market_volume->total_year = count($array['volume']);

            $market_volume->save();

            foreach ($array['facebook'] as $item) {
                $market_facebook = new BusinessMarketFacebook;
                $market_facebook->market_id = $market->id;
                $market_facebook->title = $item['title'];
                $market_facebook->link = $item['link'];
                $market_facebook->save();
            }

            foreach ($array['histories'] as $item) {
                $market_history = new BusinessMarketHistory;
                $market_history->market_id = $market->id;
                $market_history->time_action = $item['time_action']['value'] ?? 0;
                $market_history->content = $item['content'];
                $market_history->save();
            }
        }
        return response()->json($array['facebook']);
    }

    function group_facebook(Request $request)
    {
        $market_id = $request->input('market_id');
        $facebook = BusinessMarketFacebook::where('market_id', $market_id)->get();
        return response()->json($facebook);
    }

    function history_market(Request $request)
    {
        $market_id = $request->input('market_id');
        $histories = BusinessMarketHistory::where('market_id', $market_id)->get();
        return response()->json($histories);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessMarket $businessMarket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $market = BusinessMarket::with(['campuses', 'volume', 'facebook', 'history', 'cities', 'districts'])->where('id', $id)->first();
        return $market;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessMarket $businessMarket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessMarket  $businessMarket
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessMarket $businessMarket)
    {
        //
    }
}
