<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; 

use App\Models\User;
use App\Models\Customer\Contract;
use App\Models\Customer\Classes;

use App\Services\Customer\AdmissionService;
use App\Services\Customer\ContractBillService;
use App\Services\Customer\ClassService;
use Illuminate\Support\Facades\Log;

class ContractService
{
    protected $admissionService;
    protected $contractBillService;
    protected $classService;

    public function __construct(AdmissionService $admissionService, ContractBillService $contractBillService, ClassService $classService)
    {
        $this->admissionService = $admissionService;
        $this->contractBillService = $contractBillService;
        $this->classService = $classService;
    }

    public function store($request)
    {
        $user = Auth::user();

        $data = [
            "student_id" => $request['student_id'],
            "date_contract" => Carbon::parse($request['date_contract'])->format('Y-m-d'),
            "type" => $request['type'],
            "product_category_id" => $request['product_category_id'],
            "product_id" => $request['product_id'],
            "special_id" => $request['special_id'] ? $request['special_id'] : 0,
            "promotion_id" => $request['promotion_id'] ? $request['special_id'] : 0,
            "amount" => str_replace('.', '', $request['amount']),
            "amount_offer" => $request['amount_offer'] ? str_replace('.', '', $request['amount_offer']) : 0,
            "amount_promotion" => $request['amount_promotion'] ? str_replace('.', '', $request['amount_promotion']) : 0,
            "amount_special" => $request['amount_special'] ? str_replace('.', '', $request['amount_special']) : 0,
            "manage_id" => $request['manage_id'],
            "user_id" => $user->id,

            "type_study" => $request['type_study'] ?: 0,
            "campuse_id" => $request['campuse_type'] == 2 ? $request['campuse_id'] : 0, 
            "month_id" => 0,
            "time_study_id" => 0,
            "day_shift_learn_id" => 0,
            "calendar_learn_id" => 0,
        ];

        if (!$request['no_admission'] && $request['status_admission'] != 3) {
            $data['month_id'] = $request['month_id'];
            $data['time_study_id'] = $request['time_study_id'];
            $data['day_shift_learn_id'] = $request['day_shift_learn_id'];
            $data['calendar_learn_id'] = $request['calendar_learn_id'];
        }

        $contract = Contract::create($data);

        foreach ($request['bills'] as $bill) {
            $this->contractBillService->store([
                'contract_id' => $contract->id,
                'bill_type' => 1,
                'active' => 0,
                'transaction_type' => 1,
                'amount_payment' => str_replace('.', '', $bill['amount_payment']),
                'bill_number' => $bill['bill_number'],
                'date_payment' => Carbon::parse($bill['date_payment'])->format('Y-m-d'),
                'note' => $bill['note'],
            ]);
        }
        
        $contract->with([
                'product' => function($query) {
                    $query->with(['courses']);
                }
            ]);
        $classes = [];
        foreach ($request['classes'] as $class) {
            if ($class['class_id']) {
                $classes[] = $class['class_id'];
            }
        }
        
        $classAdd = Classes::whereIn('id', $classes)->get();

        $courseContract = $contract->product->courses->pluck('id')->toArray();
        $courseClass = $classAdd->pluck('course_id')->toArray();

        if (!$request['no_admission'] && $request['status_admission'] == 3) {
            foreach ($request['classes'] as $class) {
                if ($class['class_id']) {
                    if (!in_array($class['course_id'], $courseClass)) {
                        throw new \Exception('Khóa học không hợp lệ!');
                        return;
                    }

                    $this->classService->addClassContract([
                        'class_id' => $class['class_id'],
                        'contract_id' => $contract->id, 
                    ]);

                    $courseContract = array_diff($courseContract, [$class['course_id']]);
                }
            }
        }

        foreach ($courseContract as $course) {
            $this->classService->addClassContract([
                'class_id' => 0,
                'contract_id' => $contract->id, 
            ]);
        }
      
        return $contract;
    }

    public function destroy($request)
    {
        $user = Auth::user();
        $record = Contract::find($request->id); 

        $delete = $record->delete();

        return $delete;
    }
}
