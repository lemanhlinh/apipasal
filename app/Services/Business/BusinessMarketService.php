<?php

namespace App\Services\Business;

use App\Models\BusinessMarketVolume;
use App\Models\BusinessMarketStatistical;

use Illuminate\Support\Carbon;

class BusinessMarketService
{

    public function createDataForAddStatistical($campus_id, $year, $segment_id, $city_id, $district_id, $total_student) {
        $request = json_decode(json_encode([
            'type' => 1,
            'campus_id' => $campus_id,
            'year' => $year,
            'segment_id' => $segment_id,
            'city_id' => $city_id,
            'district_id' => $district_id,
            'total_student' => $total_student
        ]), false);
        return $request;
    }

    public function addStatistical($request)
    {
        $data = BusinessMarketStatistical::updateOrCreate(
            [
                'campus_id' => $request->campus_id,
                'year' => $request->year,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id
            ],
            []
        );


        //type == 1 tạo mới thị trường
        //type == 2 nếu có khách hàng mới thì cập nhật số data
        //type == 3 nếu có hợp đồng mới thì cập nhật số học viên
        /* 
            $request = {
                type: 1,
                total_student: 10
            }
            $request = {
                type: 2,
                totalDataPrimarySchoolCount: 10
            }
            $request = {
                type: 3,
                totalStudentPasalPrimarySchool: 10
            }
        */
        $type = $request->type;

        switch ($request->segment_id) {
            case 1:
                if ($type == 1) {
                    $data->primarySchoolCount = $data->primarySchoolCount + 1;
                    $data->totalStudentPrimarySchool = $data->totalStudentPrimarySchool + $request->total_student;
                }
                if ($type == 2) {
                    $data->totalDataPrimarySchoolCount = $data->totalDataPrimarySchoolCount + $request->totalDataPrimarySchoolCount;
                }
                if ($type == 3) {
                    $data->totalStudentPasalPrimarySchool = $data->totalStudentPasalPrimarySchool + $request->totalStudentPasalPrimarySchool;
                }
                break;
            case 2:
                if ($type == 1) {
                    $data->middleSchoolCount = $data->middleSchoolCount + 1;
                    $data->totalStudentMiddleSchool = $data->totalStudentMiddleSchool + $request->total_student;
                }
                if ($type == 2) {
                    $data->totalDataMiddleSchoolCount = $data->totalDataMiddleSchoolCount + $request->totalDataMiddleSchoolCount;
                }
                if ($type == 3) {
                    $data->totalStudentPasalMiddleSchool = $data->totalStudentPasalMiddleSchool + $request->totalStudentPasalMiddleSchool;
                }
                break;
            case 3:
                if ($type == 1) {
                    $data->highSchoolCount = $data->highSchoolCount + 1;
                    $data->totalStudentHighSchool = $data->totalStudentHighSchool + $request->total_student;
                }
                if ($type == 2) {
                    $data->totalDataHighSchoolCount = $data->totalDataHighSchoolCount + $request->totalDataHighSchoolCount;
                }
                if ($type == 3) {
                    $data->totalStudentPasalHighSchool = $data->totalStudentPasalHighSchool + $request->totalStudentPasalHighSchool;
                }
                break;
            case 4:
                if ($type == 1) {
                    $data->collegeCount = $data->collegeCount + 1;
                    $data->totalStudentCollege = $data->totalStudentCollege + $request->total_student;
                }
                if ($type == 2) {
                    $data->totalDataCollegeCount = $data->totalDataCollegeCount + $request->totalDataCollegeCount;
                }
                if ($type == 3) {
                    $data->totalStudentPasalCollegeSchool = $data->totalStudentPasalCollegeSchool + $request->totalStudentPasalCollegeSchool;
                }
                break;
            case 5:
                if ($type == 1) {
                    $data->workingCount = $data->workingCount + 1;
                    $data->totalStudentWorking = $data->totalStudentWorking + $request->total_student;
                }
                if ($type == 2) {
                    $data->totalDataWorkingCount = $data->totalDataWorkingCount + $request->totalDataWorkingCount;
                }
                if ($type == 3) {
                    $data->totalStudentPasalWorkingSchool = $data->totalStudentPasalWorkingSchool + $request->totalStudentPasalWorkingSchool;
                }
                break;
        }


        $data->save();
        return $data;
    }
}
