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
use App\Constants\Customer\Type;
use App\Constants\Customer\Segment;
use App\Models\Customer\CustomerSegment;
use App\Models\Customer\CustomerHistory;
use App\Models\Customer\CustomerRelated;

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
        $segments = [];

        switch ($request['segment_id']) {
            case Segment::PRIMARY_SCHOOL:
                foreach ($request['segment'] as $segmentItem) {
                    $segments[] = [
                        "name" => $segmentItem['name'],
                        "gender" => $segmentItem['gender'],
                        "district_id" => $segmentItem['district_id'],
                        "market_id" => $segmentItem['market_id'],
                        "class" => $segmentItem['class'],
                    ];
                }
                break;
            case Segment::SECONDARY_SCHOOL:
                foreach ($request['segment'] as $segmentItem) {
                    $segments[] = [
                        "name" => $segmentItem['name'],
                        "gender" => $segmentItem['gender'],
                        "district_id" => $segmentItem['district_id'],
                        "market_id" => $segmentItem['market_id'],
                        "class" => $segmentItem['class'],
                    ];
                }
                break;
            case Segment::HIGH_SCHOOL:
                $segments[] = [
                    'district_id' => $request['segment'][0]['district_id'],
                    'market_id' => $request['segment'][0]['market_id'],
                    'class' => $request['segment'][0]['class'],
                    'parent' => json_encode($request['segment'][0]['parent'], JSON_UNESCAPED_UNICODE),
                ];
                break;
            case Segment::COLLEGE:
                $segments[] = [
                    'district_id' => @$request['segment'][0]['district_id'],
                    'market_id' => @$request['segment'][0]['market_id'],
                    'college_year' => @$request['segment'][0]['college_year'],
                    'college_major' => @$request['segment'][0]['college_major'] ?: 0,
                ];
                break;
            case Segment::WORKING:
                $segments[] = [
                    'company' => $request['segment'][0]['company'],
                    'position' => $request['segment'][0]['position'],
                    'work' => $request['segment'][0]['work'],
                ];
                break;
        }

        $data = [
            'title' => $request['title'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'sex' => $request['sex'],
            'year_birth' => $request['year_birth'],
            'country_id' => $request['country_id'],
            'city_id' => $request['city_id'],
            'district_id' => $request['district_id'],
            'address' => $request['address'],
            'segment_id' => $request['segment_id'],
            'source_type_id' => $request['source_type_id'],
            'source_id' => $request['source_id'],
            'issue' => $request['issue'],
            'consulting_detail' => json_encode($request['consulting_detail'], JSON_UNESCAPED_UNICODE),
            'consulting' => $request['consulting'],
            'potential' => $request['potential'],
            'date_registration' => Carbon::parse($request['date_registration'])->format('Y-m-d'),
            'product_category_id' => $request['product_category_id'],
            'product_id' => $request['product_id'],
            'contract' => $request['contract'] ? 1 : 0,
            'manage_id' => $user->id,
            'type' => Type::NEW,
            'active' => Active::CARE,
            'active_date' => Carbon::now()->addDay($this->dayExpired)->format('Y-m-d')
        ];

        $customer = Customer::create($data);

        foreach ($segments as $segment) {
            $customer->segment()->create($segment);
        } 

        if ($request['source_id'] == Source::STUDENT) {
            CustomerRelated::create([
                'related_id' => $customer->id,
                'customer_id' => $request['source_id']
            ]);
        } 

        return $customer;
    }

    public function destroy($request)
    {
        $user = Auth::user();
        $customer = Customer::find($request->id);

        $customer->segment()->delete();

        if ($customer->students) {
            foreach ($customer->students as $student) {
                if ($student->contracts) {
                    foreach ($student->contracts as $contract) {
                        $contract->bills()->delete();
                        $contract->classes()->delete();
                        $contract->debts()->delete();
                        $contract->delete();
                    }
                }
                $student->delete();
            }
        }

        $customer->histories()->delete();

        $customer->related()->detach();
        CustomerRelated::where('related_id', $customer->id)->orWhere('customer_id', $customer->id)->delete();

        $delete = $customer->delete();

        return $delete;
    }

    public function updateActive()
    {
        $today = Carbon::today()->format('Y-m-d');

        Customer::where('active', Type::NEW)
            ->whereDate('active_date', $today)
            ->update(['active' => Type::DEPOT]);
    }

    public function addHistory($data)
    {
        return CustomerHistory::create($data);
    }

    public function update($data, $customer)
    {
        $user = Auth::user();

        foreach ($data as $field => $value) {
            $fieldChange[] = $field;
            $oldValue[] = $customer->$field;
            $newValue[] = $value;
        }

        $this->addHistory([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'field' => json_encode($fieldChange, JSON_UNESCAPED_UNICODE),
            'old_value' => json_encode($oldValue, JSON_UNESCAPED_UNICODE),
            'new_value' => json_encode($newValue, JSON_UNESCAPED_UNICODE),
        ]);

        $customer->update($data);

        return $customer;
    }
}
