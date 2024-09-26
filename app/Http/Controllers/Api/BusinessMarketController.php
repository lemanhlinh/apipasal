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
use Illuminate\Support\Facades\Bus;
use App\Services\Business\BusinessMarketService;

class BusinessMarketController extends Controller
{
    private $bussinessMarketService;

    public function __construct()
    {
        $this->bussinessMarketService = new BusinessMarketService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $city_id = request('city_id');
        $district_id = request('district_id');
        $campuses_id = request('campuses_id');
        $segment = request('segment');

        $query = BusinessMarket::with(['campuses', 'volume', 'facebook', 'history', 'cities', 'districts'])->orderBy('id', 'DESC');

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

    public function saveStatistical()
    {
        $marketStatistical = BusinessMarketStatistical::where('year', '=', request('year'))
            ->where('campus_id', '=', request('campus_id'))
            ->where('city_id', '=', request('city_id'))
            ->where('district_id', '=', request('district_id'))
            ->firstOrCreate([]);

        $validatedData = request()->validate([
            'middleSchoolCount' => 'nullable|integer',
            'primarySchoolCount' => 'nullable|integer',
            'highSchoolCount' => 'nullable|integer',
            'collegeCount' => 'nullable|integer',
            'workingCount' => 'nullable|integer',
            'totalStudentPrimarySchool' => 'nullable|integer',
            'totalStudentMiddleSchool' => 'nullable|integer',
            'totalStudentHighSchool' => 'nullable|integer',
            'totalStudentCollege' => 'nullable|integer',
            'totalStudentWorking' => 'nullable|integer',
            'totalDataPrimarySchoolCount' => 'nullable|integer',
            'totalDataMiddleSchoolCount' => 'nullable|integer',
            'totalDataHighSchoolCount' => 'nullable|integer',
            'totalDataCollegeCount' => 'nullable|integer',
            'totalDataWorkingCount' => 'nullable|integer',
            'totalStudentPasalPrimarySchool' => 'nullable|integer',
            'totalStudentPasalMiddleSchool' => 'nullable|integer',
            'totalStudentPasalHighSchool' => 'nullable|integer',
            'totalStudentPasalCollegeSchool' => 'nullable|integer',
            'totalStudentPasalWorkingSchool' => 'nullable|integer',
            'year' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'campus_id' => 'nullable|integer',
        ]);

        $marketStatistical->middleSchoolCount += $validatedData['middleSchoolCount'] ?? 0;
        $marketStatistical->primarySchoolCount += $validatedData['primarySchoolCount'] ?? 0;
        $marketStatistical->highSchoolCount += $validatedData['highSchoolCount'] ?? 0;
        $marketStatistical->collegeCount += $validatedData['collegeCount'] ?? 0;
        $marketStatistical->workingCount += $validatedData['workingCount'] ?? 0;

        $marketStatistical->totalStudentPrimarySchool += $validatedData['totalStudentPrimarySchool'] ?? 0;
        $marketStatistical->totalStudentMiddleSchool += $validatedData['totalStudentMiddleSchool'] ?? 0;
        $marketStatistical->totalStudentHighSchool += $validatedData['totalStudentHighSchool'] ?? 0;
        $marketStatistical->totalStudentCollege += $validatedData['totalStudentCollege'] ?? 0;
        $marketStatistical->totalStudentWorking += $validatedData['totalStudentWorking'] ?? 0;

        $marketStatistical->totalDataPrimarySchoolCount += $validatedData['totalDataPrimarySchoolCount'] ?? 0;
        $marketStatistical->totalDataMiddleSchoolCount += $validatedData['totalDataMiddleSchoolCount'] ?? 0;
        $marketStatistical->totalDataHighSchoolCount += $validatedData['totalDataHighSchoolCount'] ?? 0;
        $marketStatistical->totalDataCollegeCount += $validatedData['totalDataCollegeCount'] ?? 0;
        $marketStatistical->totalDataWorkingCount += $validatedData['totalDataWorkingCount'] ?? 0;

        $marketStatistical->totalStudentPasalPrimarySchool += $validatedData['totalStudentPasalPrimarySchool'] ?? 0;
        $marketStatistical->totalStudentPasalMiddleSchool += $validatedData['totalStudentPasalMiddleSchool'] ?? 0;
        $marketStatistical->totalStudentPasalHighSchool += $validatedData['totalStudentPasalHighSchool'] ?? 0;
        $marketStatistical->totalStudentPasalCollegeSchool += $validatedData['totalStudentPasalCollegeSchool'] ?? 0;
        $marketStatistical->totalStudentPasalWorkingSchool += $validatedData['totalStudentPasalWorkingSchool'] ?? 0;

        $marketStatistical->year = $validatedData['year'] ?? 0;
        $marketStatistical->city_id = $validatedData['city_id'] ?? 0;
        $marketStatistical->district_id = $validatedData['district_id'] ?? 0;
        $marketStatistical->campus_id = $validatedData['campus_id'] ?? 0;

        $marketStatistical->save();
        return response()->json([
            'message' => 'Thống kê dữ liệu thị trường thành công!',
            'data' => $marketStatistical
        ]);
    }

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
        $campuses = Campuses::whereIn('code', $array['campuses'])->get();
        $campusesIds = $campuses->pluck('id')->toArray();        
        $market->campuses_code = json_encode($array['campuses']);
        $market->campuses_id = json_encode($campusesIds);

        $market->total_student = $array['total_student'];
        $market->save();

        foreach ($array['campuses'] as $item) {
            $campus = Campuses::where('code', $item)->first();
            $request = $this->bussinessMarketService->createDataForAddStatistical(
                $campus->id, 
                $array['year']['value'], 
                $array['segment'], 
                $array['city_id'], 
                $array['district_id'], 
                $array['total_student'],
                1
            );
            $this->bussinessMarketService->addStatistical($request);
        }
        if ($market->id) {

            if (!empty($array['volumes'])) {
                foreach ($array['volumes'] as $volume) {
                    $market_volume = new BusinessMarketVolume;
                    $market_volume->market_id = $market->id;
                    $market_volume->year = $volume['year']['value'] ?? 0;
                    $market_volume->more_level = json_encode($volume['items']);
                    $market_volume->total_year = count($volume['items']);
                    $market_volume->total_student = $array['total_student'];
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


            return response()->json(['success' => true, 'message' => 'Market saved successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to save market'], 500);
    }

    function thong_ke_thi_truong($item) {

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
    public function edit(Request $request, $id)
    {
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
            'total_student'
        ];

        foreach ($fields as $field) {
            if (!empty($array[$field])) {
                $market->$field = $array[$field];
            }
        }
        if (!empty($array['campuses'])) {
            $market->campuses_id = json_encode($array['campuses']);
        }
        $market->active = 1;

        $market->save();

        if ($market->id) {
            BusinessMarketVolume::where('market_id', $market->id)->delete();
            foreach ($array['volumes'] as $volume) {
                $market_volume = new BusinessMarketVolume;
                $market_volume->market_id = $market->id;
                $market_volume->year = $volume['year']['value'] ?? 0;
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


            return response()->json(['success' => true, 'message' => 'Market updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update market'], 500);
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
