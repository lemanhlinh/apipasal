<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Customer\Customer;
use App\Models\Customer\Student;
use App\Models\Customer\StudentStatus;
use App\Models\Customer\CustomerSegment;

use App\Services\Customer\ContractService;
use App\Services\Customer\AdmissionService;

use App\Constants\Customer\Source;
use App\Constants\Customer\Consulting;
use App\Constants\Customer\Active;
use App\Constants\Customer\Type;
use App\Constants\Customer\Segment;

class StudentService
{
    protected $contractService;
    protected $admissionService;

    public function __construct(ContractService $contractService, AdmissionService $admissionService)
    {
        $this->contractService = $contractService;
        $this->admissionService = $admissionService;
    }

    public function store($request)
    {
        $user = Auth::user();

        $customer = Customer::find($request['customer_id']);

        $customer->update([
            'birthday' => $request['customer']['birthday'],
            'active' => Active::CONTRACT,
            'type' => Type::NEW,
            'active_date' => null
        ]);

        CustomerSegment::find($request['customer_segment_id'])->update([
            'birthday' => @$request['segment']['birthday'] ? Carbon::parse($request['segment']['birthday'])->format('Y-m-d') : NULL,
            'telephone' => @$request['telephone'] ? $request['telephone'] : NULL,
        ]);

        $student = Student::updateOrCreate([
            'customer_id' => $request['customer_id'],
            'customer_segment_id' => $request['customer_segment_id']
        ]);

        foreach ($request['contracts'] as $contract) {
            $contract['student_id'] = $student->id;
            $contract['active'] = 0;
            $this->contractService->store($contract);
        }

        return $student;
    }
}
