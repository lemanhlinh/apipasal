<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMarketStatistical extends Model
{
    use HasFactory;

    protected $table = 'business_market_statistical';
    protected $fillable = [
        'campus_id',
        'year',
        'city_id',
        'district_id',
        'primarySchoolCount',
        'middleSchoolCount',
        'highSchoolCount',
        'collegeCount',
        'workingCount',
        'totalStudentPrimarySchool',
        'totalStudentMiddleSchool',
        'totalStudentHighSchool',
        'totalStudentCollege',
        'totalStudentWorking',
        'totalDataPrimarySchoolCount',
        'totalDataMiddleSchoolCount',
        'totalDataHighSchoolCount',
        'totalDataCollegeCount',
        'totalDataWorkingCount',
        'totalStudentPasalPrimarySchool',
        'totalStudentPasalMiddleSchool',
        'totalStudentPasalHighSchool',
        'totalStudentPasalCollegeSchool',
        'totalStudentPasalWorkingSchool',
        'total_student'
    ];
}
