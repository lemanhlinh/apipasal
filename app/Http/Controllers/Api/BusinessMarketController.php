<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessMarket;
use App\Models\BusinessMarketFacebook;
use App\Models\BusinessMarketHistory;
use App\Models\BusinessMarketVolume;
use App\Models\Campuses;
use Illuminate\Http\Request;
use App\Models\BusinessMarketStatistical;
use App\Services\Business\BusinessMarketService;
use Illuminate\Support\Facades\DB;

class BusinessMarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return DB::transaction(function () {
            $city_id = request('city_id');
            $district_id = request('district_id');
            $campuses_id = request('campuses_id');
            $segment = request('segment');

            $query = BusinessMarket::with(['volumes', 'facebook', 'histories', 'cities', 'districts'])->orderBy('id', 'DESC');

            if ($city_id) {
                $query->where('city_id', $city_id);
            }
            if ($district_id) {
                $query->where('district_id', $district_id);
            }
            if (is_array($campuses_id) && !empty($campuses_id)) {
                $query->where(function ($query) use ($campuses_id) {
                    foreach ($campuses_id as $id) {
                        $query->orWhere('campuses_id', 'like', '%' . $id . '%');
                    }
                });
            }

            if ($segment) {
                $query->where('segment', $segment);
            }

            $markets = $query->paginate(15);

            $campusesIds = [];
            foreach ($markets as $market) {
                $campusesIds = array_merge($campusesIds, json_decode($market->campuses_id));
                $campuses = Campuses::whereIn('id', $campusesIds)->get()->keyBy('id');
            }

            foreach ($markets as $market) {
                $market->campuses = collect(json_decode($market->campuses_id, true))->map(function ($id) use ($campuses) {
                    return $campuses->get($id);
                });
            }

            $markets->getCollection()->transform(function ($market) {
                $market->total_volume = $market->total_volume;
                return $market;
            });

            return response()->json([
                'data' => $markets
            ], 200);
        });
    }

    public function detail($id)
    {
        $market = BusinessMarket::with(['volumes', 'facebook', 'histories', 'cities', 'districts'])->find($id);
        if (!$market) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json([
            'data' => $market
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function topHocVienThiTruong()
    {
        $records = BusinessMarketVolume::where(function ($query) {
            $data = request()->all();
            $market_id = $data['market_id'] ?? '';
            $year = $data['year'] ?? '';

            if ($market_id) {
                $query->where('market_id', $market_id);
            }
            if ($year) {
                $query->where('year', $year);
            }
        })->with('market')->orderBy('total_student_pasal', 'desc')->limit(10)->get();

        $years = BusinessMarketVolume::select('year')->distinct()->orderBy('year')->pluck('year');
        $total_student_pasal = 0;

        foreach ($records as $record) {
            $total_student_pasal += $record->total_student_pasal;
        }
        return response()->json([
            'sucess' => true,
            'data' => array(
                'years' => $years,
                'data' => $records,
                'total_student_pasal' => $total_student_pasal
            ),
        ], 200);
    }

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

    public function statistical()
    {
        $query = new BusinessMarketStatistical();
        if (request()->input('year')) {
            $query = $query->where('year', request()->input('year'));
        }
        if (request()->input('campus_id')) {
            $query = $query->where('campus_id', request()->input('campus_id'));
        }
        if (request()->input('city_id')) {
            $query = $query->where('city_id', request()->input('city_id'));
        }
        if (request()->input('district_id')) {
            $district_id = request()->input('district_id');
            $query = $query->whereIn('district_id', $district_id);
        }
        $marketStatistical = $query->get();

        $aggregatedData = $marketStatistical->reduce(function ($carry, $item) {
            foreach ($item->toArray() as $key => $value) {
                if (in_array($key, ['year', 'id', 'district_id', 'city_id', 'campus_id', 'updated_at', 'created_at'])) {
                    continue;
                }
                if (is_numeric($value)) {
                    $carry[$key] = ($carry[$key] ?? 0) + $value;
                } else {
                    $carry[$key] = $value;
                }
            }
            return $carry;
        }, []);

        return response()->json([
            'data' => array(
                'detail' => $marketStatistical,
                'total' => $aggregatedData
            )
        ]);
    }

    public function store(Request $request)
    {
        try {
            $result = DB::transaction(function() use ($request) {
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
                $list_campuses = Campuses::whereIn('code', $array['campuses'])->get();
                $campusesIds = $list_campuses->pluck('id')->toArray();
                $market->campuses_code = json_encode($array['campuses']);
                $market->campuses_id = json_encode($campusesIds);
    
                $market->total_student = $array['total_student'];
                $market->save();
    
                if ($market->id) {
                    if (!empty($array['volumes'])) {
                        foreach ($array['volumes'] as $volume) {
                            $market_volume = new BusinessMarketVolume;
                            $market_volume->market_id = $market->id;
                            $market_volume->year = $volume['year'];
                            $market_volume->total_student = $volume['total_student'] ?? 0;
                            $market_volume->more_level = json_encode($volume['items']);
                            $market_volume->total_year = count($volume['items']);
                            $market_volume->save();
                        }
                    }
    
                    if (!empty($array['facebook'])) {
                        foreach ($array['facebook'] as $item) {
                            $market_facebook = new BusinessMarketFacebook;
                            $market_facebook->market_id = $market->id;
                            $market_facebook->title = $item['title'];
                            $market_facebook->link = $item['link'];
                            $market_facebook->save();
                        }
                    }
    
                    if (!empty($array['histories'])) {
                        foreach ($array['histories'] as $item) {
                            $market_history = new BusinessMarketHistory;
                            $market_history->market_id = $market->id;
                            $market_history->time_action = $item['time_action']['value'] ?? 0;
                            $market_history->content = $item['content'];
                            $market_history->save();
                        }
                    }
    
                    return ['success' => true, 'message' => 'Thêm mới thị trường thành công'];
                }
    
                return ['success' => false, 'message' => 'Chưa thêm được thị trường'];
            });
    
            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    function thong_ke_thi_truong($item) {}

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
    public function edit(Request $request, $id)
    {
        try {
            $result = DB::transaction(function () use ($request, $id) {
                $array = $request->all();
    
                $market = BusinessMarket::findOrFail($id);
                $fields = [
                    'title',
                    'segment',
                    'link_map',
                    'city_id',
                    'district_id',
                    'potential',
                    'note',
                    'total_student',
                    'campuses_id',
                ];
    
                foreach ($fields as $field) {
                    if (!empty($array[$field])) {
                        $market->$field = $array[$field];
                    }
                }
      
                $market->active = 1;
    
                $market->save();
    
                if ($market->id) {
                    BusinessMarketVolume::where('market_id', $market->id)->delete();
                    foreach ($array['volumes'] as $volume) {
                        $market_volume = new BusinessMarketVolume;
                        $market_volume->market_id = $market->id;
                        $market_volume->year = $volume['year'];
                        $market_volume->total_student = $volume['total_student'] ?? 0;
                        $market_volume->more_level = json_encode($volume['items']);
                        $market_volume->total_year = count($volume['items']);
                        $market_volume->save();
                    }
    
                    BusinessMarketFacebook::where('market_id', $market->id)->delete();
                    if (!empty($array['facebook'])) {
                        foreach ($array['facebook'] as $item) {
                            $market_facebook = new BusinessMarketFacebook;
                            $market_facebook->market_id = $market->id;
                            $market_facebook->title = $item['title'];
                            $market_facebook->link = $item['link'];
                            $market_facebook->save();
                        }
                    }
    
    
                    BusinessMarketHistory::where('market_id', $market->id)->delete();
                    if (!empty($array['histories'])) {
                        foreach ($array['histories'] as $item) {
                            $market_history = new BusinessMarketHistory;
                            $market_history->market_id = $market->id;
                            $market_history->time_action = $item['time_action']['value'] ?? 0;
                            $market_history->content = $item['content'];
                            $market_history->save();
                        }
                    }

                    return ['success' => true, 'message' => 'Cập nhật thị trường thành công'];
                }
    
                return ['success' => false, 'message' => 'Cập nhật thị trường thất bại'];
            });
            if($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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

