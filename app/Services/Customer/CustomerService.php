<?php

namespace App\Services\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerStatus;

use App\Services\Business\BusinessPartnerService;

use App\Constants\Customer\Source;
use App\Constants\Customer\Consulting;
use App\Constants\Customer\Active;
use App\Constants\Customer\Segment;
use App\Models\Customer\CustomerSegment;

class CustomerService
{
    protected $businessPartnerService;
    private $dayExpired = 30;

    public function __construct(BusinessPartnerService $businessPartnerService)
    {
        $this->businessPartnerService = $businessPartnerService;
    }

    public function store($request)
    {
         
        $user = Auth::user();
        $segmentDetail = [];

        switch ($request->segment) {
            case Segment::PRIMARY_SCHOOL:
                foreach ($request->segmentInfo['children'] as $child) {
                    $segmentDetail[] = [
                        "name" => $child['name'],
                        "gender" => $child['gender'],
                        "district_id" => $child['district_id'],
                        "market_id" => $child['market_id'],
                        "class" => $child['class'],
                    ];
                }
                break;
            case Segment::SECONDARY_SCHOOL:
                foreach ($request->segmentInfo['children'] as $child) {
                    $segmentDetail[] = [
                        "name" => $child['name'],
                        "gender" => $child['gender'],
                        "district_id" => $child['district_id'],
                        "market_id" => $child['market_id'],
                        "class" => $child['class'],
                    ];
                }
                break;
            case Segment::HIGH_SCHOOL:
                $segmentDetail[] = [
                    'district_id' => $request->segmentInfo['district_id'],
                    'market_id' => $request->segmentInfo['market_id'],
                    'class' => $request->segmentInfo['class'],
                    'parent' => json_encode($request->segmentInfo['parent'], JSON_UNESCAPED_UNICODE),
                ];
                break;
            case Segment::COLLEGE:
                $segmentDetail[] = [
                    'district_id' => $request->segmentInfo['district_id'],
                    'market_id' => $request->segmentInfo['market_id'],
                    'college_year' => $request->segmentInfo['college_year'],
                    'college_major' => $request->segmentInfomajor['college_major'],
                ];
                break;
            case Segment::WORKING:
                $segmentDetail[] = [
                    'company' => $request->segmentInfo['company'],
                    'position' => $request->segmentInfo['position'],
                    'work' => $request->segmentInfo['work'],
                ];
                break;
        }

        $data = [
            'title' => $request->title,
            'phone' => $request->phone,
            'email' => $request->email,
            'sex' => $request->sex,
            'year_birth' => $request->year_birth,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'district_id' => $request->district,
            'address' => $request->address,
            'segment' => $request->segment,
            'segment_detail' => '[]',
            'source' => $request->source,
            'source_detail' => $request->source_detail,
            'issue' => $request->issue,
            'consulting_detail' => json_encode($request->consulting_detail, JSON_UNESCAPED_UNICODE),
            'consulting' => $request->consulting,
            'potential' => $request->potential,
            'date_registration' => Carbon::parse($request->date_registration)->format('Y-m-d'),
            'product_category_id' => $request->product_category,
            'product_id' => $request->product,
            'contract' => $request->contract ? 1 : 0,
            'manage_id' => $user->id,
            'active' => Active::NEW,
            'active_date' => Carbon::now()->addDay($this->dayExpired)->format('Y-m-d')
        ];

        $customer = Customer::create($data);

        foreach ($segmentDetail as $segment) {
            $customer->segment_info()->create($segment);
        }

        if ($request->source == Source::PARTNER) {
            $this->businessPartnerService->updateStatus($request->source_detail);
        }

        $this->updateSingleStatus($user->id);

        return $customer;
    }


    public function destroy($request)
    {
        $user = Auth::user();
        $record = Customer::find($request->id);

        $delete = $record->delete();

        $this->updateSingleStatus($user->id);

        return $delete;
    }

    public function updateSingleStatus($userId)
    {
        $today = Carbon::today()->format('Y-m-d');
        $customers = Customer::whereDate('created_at', $today)->where('manage_id', $userId)->get();

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

        $primary_school = 0;
        $secondary_school = 0;
        $high_school = 0;
        $college = 0;
        $working = 0;

        $success = 0;
        $new = 0;
        $depot = 0;
        $total = 0;

        $contract_total = 0;
        $contract_success = 0;
        $contract_expired = 0;
        $contract_no_history = 0;

        foreach ($customers as $customer) {
            $dateRegistration = Carbon::parse($customer->date_registration)->format('Y-m-d');

            if ($customer->consulting_date) {
                $consultingDate = Carbon::parse($customer->consulting_date);

                $dateDiff = Carbon::today()->diffInDays($consultingDate);

                if ($customer->contract && $customer->consulting != Consulting::CANCEL && $customer->active != Active::STUDENT && $dateDiff > 3) {
                    $contract_no_history++;
                }
            }

            $total++;

            switch ($customer->segment) {
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

            if ($customer->contract) {
                $contract_total++;
            }

            if ($customer->contract && $customer->active == Active::STUDENT) {
                $contract_success++;
            }

            if ($customer->active == Active::DEPOT) {
                $depot++;
            } else if ($customer->active == Active::NEW) {
                $new++;
            } else if ($customer->active == Active::STUDENT) {
                $success++;
            }

            if ($today > $dateRegistration) {
                $contract_expired++;
            }
        }

        $status = CustomerStatus::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            [
                'primary_school' => $primary_school,
                'secondary_school' => $secondary_school,
                'high_school' => $high_school,
                'college' => $college,
                'working' => $working,

                'customer_success' => $success,
                'customer_new' => $new,
                'customer_depot' => $depot,
                'customer_total' => $total,

                'contract_total' => $contract_total,
                'contract_success' => $contract_success,
                // 'contract_percent' => round($contract_total / $total * 100, 2),
                // 'contract_success_percent' => round($contract_success / $contract_total * 100, 2),
                'contract_expired' => $contract_expired
            ]
        );

        return $status;
    }

    public function updateActive()
    {
        $today = Carbon::today()->format('Y-m-d');

        Customer::where('active', Active::NEW)
            ->whereDate('active_date', $today)
            ->update(['active' => Active::DEPOT]);
    }
}
