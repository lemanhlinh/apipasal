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

        // $this->admissionService->store($request);

        // $this->updateSingleStatus($user->id);

        return $student;
    }

    public function updateSingleStatus($userId)
    {
        $today = Carbon::today()->format('Y-m-d');

        $students = Student::whereDate('created_at', $today)
            ->with(['customer', 'contracts'])
            ->whereHas('customer', function ($query) use ($userId) {
                $query->where('manage_id', $userId);
            })
            ->get();

        return $this->updateStatus($students, $userId);
    }

    public function updateMultipleStatus()
    {
        $users = User::where('active', 1)
            ->with(['customers' => function ($query) {
                $query->select('id', 'manage_id', 'segment', 'contract', 'active', 'active_date', 'date_registration', 'consulting_date', 'consulting', 'source', 'source_detail')
                    ->with(['students' => function ($queryStudent) {
                        $queryStudent->with('contracts');
                    }]);
            }])
            ->get();

        foreach ($users as $user) {
            foreach ($user->customers as $customer) {
                foreach ($customer->students as $student) {
                    $student->customer = $customer;
                }
            }
        }

        foreach ($users as $user) {
            $students = $user->customers->flatMap(function ($customer) {
                return $customer->students;
            });

            $status = $this->updateStatus($students, $user->id);
        }

        return;
    }

    public function updateStatus($students, $userId)
    {
        $today = Carbon::today()->format('Y-m-d');

        $primary_school = 0;
        $secondary_school = 0;
        $high_school = 0;
        $college = 0;
        $working = 0;
        $days = 0;
        $contract = 0;

        foreach ($students as $student) {
            $days += Carbon::parse($student->created_at)->diffInDays(Carbon::parse($student->customer->created_at));

            switch ($student->customer->segment_id) {
                case Segment::PRIMARY_SCHOOL:
                    $primary_school++;
                    break;
                case Segment::SECONDARY_SCHOOL:
                    $secondary_school++;
                    break;
                case Segment::HIGH_SCHOOL:
                    $high_school++;
                    break;
                case Segment::COLLEGE:
                    $college++;
                    break;
                case Segment::WORKING:
                    $working++;
                    break;
            }

            foreach ($student->cotracts as $contract) {
                $contract += $contract->amount;
            }
        }

        $status = StudentStatus::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            [
                'primary_school' => $primary_school,
                'secondary_school' => $secondary_school,
                'high_school' => $high_school,
                'college' => $college,
                'working' => $working,

                'contract' => $contract,
                'days' => round($days / count($students), 1)
            ]
        );

        return $status;
    }
}
