<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; 

use App\Models\User;
use App\Models\Customer\Contract;
use App\Models\Customer\ContractStatus;

use App\Services\Customer\AdmissionService;
use App\Services\Customer\ContractBillService;

class ContractService
{
    protected $admissionService;
    protected $contractBillService;

    public function __construct(AdmissionService $admissionService, ContractBillService $contractBillService)
    {
        $this->admissionService = $admissionService;
        $this->contractBillService = $contractBillService;
    }

    public function store($request)
    {
        $user = Auth::user();

        $data = [
            "student_id" => $request->student_id,
            "date_contract" => Carbon::parse($request->date_contract)->format('Y-m-d'),
            "type" => $request->type,
            "product_category_id" => $request->product_category_id,
            "product_id" => $request->product_id,
            "special_id" => $request->special_id,
            "promotion_id" => $request->promotion_id,
            "offer_extra" => $request->offer_extra,
            "manage_id" => $request->manage_id,
            "amount" => $request->amount,
            "bill_number" => $request->bill_number,
            "date_payment" => Carbon::parse($request->date_payment)->format('Y-m-d'),
            "note" => $request->note,
        ];

        $contract = Contract::create($data);

        foreach ($request['bills'] as $bill) {
            $this->contractBillService->store([
                'contract_id' => $contract->id,
                'bill_type' => 1,
                'transaction_type' => 1,
                'amount_payment' => str_replace('.', '', $bill['amount_payment']),
                'bill_number' => $bill['bill_number'],
                'date_payment' => Carbon::parse($bill['date_payment'])->format('Y-m-d'),
                'note' => $bill['note'],
            ]);
        }       

        $this->updateSingleStatus($user->id);

        $this->admissionService->store($request);

        return $contract;
    }


    public function destroy($request)
    {
        $user = Auth::user();
        $record = Contract::find($request->id); 

        $delete = $record->delete();

        $this->updateSingleStatus($user->id);

        return $delete;
    }

    public function updateSingleStatus($userId)
    {
        $today = Carbon::today()->format('Y-m-d');
        $customers = Contract::whereDate('created_at', $today)->where('manage_id', $userId)->get();

        return $this->updateStatus($customers, $userId);
    }

    public function updateMultipleStatus()
    {
        $users = User::where('active', 1)
            ->with(['customers' => function ($query) {
                $query->select('id', 'manage_id', 'segment', 'contract', 'active', 'active_date', 'date_registration', 'consulting_date', 'consulting', 'source', 'source_detail');
            }])
            ->get();

        foreach ($users as $user) {
            $status = $this->updateStatus($user->customers, $user->id);
        }

        return;
    }

    public function updateStatus($customers, $userId)
    {
        $today = Carbon::today()->format('Y-m-d');

        return;
    }
}
