<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\BusinessSettingDemoExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Models\Customer\Demo;
use App\Models\Customer\Customer;
use App\Models\Customer\DemoCustomer;

use App\Constants\Customer\Type;
use App\Models\CustomerDemo;

class DemoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $queryDemo = Demo::orderBy('id', 'DESC')
            ->where('user_id', $user->id)
            ->with([
                'user_manage' => function ($query) {
                    $query->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                        $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                            $query3->select('campuses.id', 'campuses.code');
                        }]);
                    }]);
                },
                'demo' => function ($query) {
                    $query->select('id', 'title');
                },
                'campuses' => function ($query) {
                    $query->select('id', 'title');
                },
                'demo_customer' => function ($query) {
                    $query->orderBy('id', 'desc')->with([
                        'customer' => function ($queryCustomer) {
                            $queryCustomer->with(['management' => function ($queryManagement){
                                $queryManagement->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                                    $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                                        $query3->select('campuses.id', 'campuses.code');
                                    }]);
                                }]);
                            }]);
                        },
                    ]);
                }
            ]);

        $demo = $queryDemo->paginate(20);

        $totalPages = $demo->lastPage();
        $data = $demo->items(); 

        return response()->json(array(
            'error' => false,
            'data' => [
                'data' => $data,
                'total_pages' => $totalPages,
            ],
        ));
    }

    public function store(Request $request)
    {
        // return response()->json(array(
        //     'error' => false,
        //     'message' => 'Cập nhật khách hàng thành công!',
        //     'data' => $request->all()
        // ));

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $data = [
                'title' => $request->title,
                'campuses_type' => $request->campuses_type,
                'campuses_id' => $request->campuses_type == 3 ? $request->campuses_id : 0,
                'demo_id' => $request->demo_id,
                'type' => $request->type,
                'address' => $request->address,
                'date_start' => Carbon::parse($request->date_start)->format('Y-m-d'),
                'date_end' => Carbon::parse($request->date_end)->format('Y-m-d'),
                'schedule' => $request->schedule,
                'study' => $request->study,
                'speaker_id' => 0,
                'user_manage_id' => $request->user_manage_id,
                'user_id' => $user->id,
                'active' => 1,
                'invite' => 0,
            ];

            $demo = Demo::create($data);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $demo,
                'message' => 'Đã thêm mới Demo trải nghiệm!',
            ));
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info([
                'message' => $ex->getMessage(),
                'line' => __LINE__,
                'method' => __METHOD__
            ]);
            return response()->json(array(
                'error' => true,
                'message' => 'Chưa thêm được Demo trải nghiệm!',
            ));
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $demo = Demo::find($request->id);

        if (!$demo) {
            return response()->json(array(
                'error' => true,
                'message' => 'Không tìm thấy demo!',
            ));
        }

        if ($request->has('active')) {
            $data['active'] = $request->active;
        } else {
            if ($request->has('date_start')) {
                $data['date_start'] = Carbon::parse($request->date_start)->format('Y-m-d');
            }

            if ($request->has('date_end')) {
                $data['date_end'] = Carbon::parse($request->date_end)->format('Y-m-d');
            }

            if ($request->has('campuses_id')) {
                $data['campuses_id'] = $request->campuses_id;
            }

            if ($request->has('campuses_type')) {
                $data['campuses_type'] = $request->campuses_type;
            }

            if ($request->has('schedule')) {
                $data['schedule'] = $request->schedule;
            }

            if ($request->has('study')) {
                $data['study'] = $request->study;
            }

            if ($request->has('demo_id')) {
                $data['demo_id'] = $request->demo_id;
            }

            if ($request->has('type')) {
                $data['type'] = $request->type;
            }

            if ($request->has('user_id')) {
                $data['user_id'] = $request->user_id;
            }

            if ($request->has('active')) {
                $data['active'] = $request->active;
            }

            switch ($request->campuses_type) {
                case 1:
                    $typeTitle = 'PKD';
                    break;
                case 2:
                    $typeTitle = 'PĐT';
                    break;
                case 3:
                    $typeTitle = 'Trung tâm';
                    break;
            }

            if (@$data['demo_id'] && @$data['demo_id'] != $demo->demo_id) {
                $demoTitle = BusinessSettingDemoExperience::find($request->demo_id)->title;
            } else {
                $demoTitle = $demo->demo->title;
            }

            switch ($request->schedule) {
                case 1:
                    $scheduleTitle = 'Sáng';
                    break;
                case 2:
                    $scheduleTitle = 'Chiều';
                    break;
                case 3:
                    $scheduleTitle = 'Tối';
                    break;
            }

            $daysOfWeek = [
                'Monday' => 'Thứ Hai',
                'Tuesday' => 'Thứ Ba',
                'Wednesday' => 'Thứ Tư',
                'Thursday' => 'Thứ Năm',
                'Friday' => 'Thứ Sáu',
                'Saturday' => 'Thứ Bảy',
                'Sunday' => 'Chủ Nhật',
            ];
            $dayOfWeekText = $daysOfWeek[date('l', strtotime($request->date_start))];
            $dateString = Carbon::parse($request->date_start)->format('d/m/Y');

            $data['title'] = "$typeTitle-$demoTitle-$scheduleTitle-$request->study-$dayOfWeekText $dateString";
        }

        return $this->handleTransaction(function () use ($data, $demo) {

            $demo->update($data);
            $demo->demo;
            $demo->user_manage;
            $demo->campuses;

            return response()->json(array(
                'error' => false,
                'data' => $demo,
                'message' => 'Cập nhật demo thành công!',
            ));
        }, 'Cập nhật demo không thành công!');
    }

    public function remove(Request $request)
    {
        $demo = Demo::find($request->id);

        if (!$demo) {
            return response()->json(array(
                'error' => true,
                'message' => 'Không tìm thấy demo!',
            ));
        }

        return $this->handleTransaction(function () use ($demo) {
            $demo->delete();

            DemoCustomer::where('demo_id', $demo->id)->delete();

            return response()->json(array(
                'error' => false,
                'message' => 'Xóa demo thành công!',
            ));
        }, 'Xóa demo không thành công!');
    }

    public function addCustomer(Request $request)
    {
        $demoId = $request->demo_id;
        $customerTelephone = $request->customer_telephone;

        $demo = Demo::find($demoId);
        $customer = Customer::where('phone', $customerTelephone)->first();

        if (!$demo) {
            return response()->json(array(
                'error' => true,
                'data' => [],
                'message' => 'Không tìm thấy chương trình Demo!',
            ));
        }

        if (!$customer) {
            return response()->json(array(
                'error' => true,
                'data' => [],
                'type' => 'no_customer',
                'message' => 'Khách hàng chưa tồn tại. Bạn có muốn thêm mới khách hàng?',
            ));
        }

        $demoCustomer = DemoCustomer::where('demo_id', $demoId)->where('customer_id', $customer->id)->first();

        if ($demoCustomer) {
            return response()->json(array(
                'error' => true,
                'data' => [], 
                'message' => 'Khách hàng này đã tồn tại trong chương trình Demo!',
            ));
        }

        if (
            $customer->type == Type::DEPOT ||
            ($customer->active_date && (Carbon::parse($customer->active_date)->isToday()
                || Carbon::parse($customer->active_date)->isBefore(Carbon::now()))
            )
        ) {
            return response()->json(array(
                'error' => true,
                'data' => $customer,
                'type' => 'depot_customer',
                'message' => 'Khách hàng này đã về kho. Bạn có muốn nhập lại khách hàng này?',
            ));
        }

        return $this->handleTransaction(function () use ($demo, $customer) {
            $demoCustomer = DemoCustomer::create([
                'demo_id' => $demo->id,
                'customer_id' => $customer->id,
                'join' => NULL,
                'sign' => NULL,
            ]);

            $demoCustomer->load([
                'customer' => function ($queryCustomer) {
                    $queryCustomer->with(['management' => function ($queryManagement){
                        $queryManagement->select('id', 'name', 'department_id')->with(['department' => function ($query2) {
                            $query2->select('id', 'title')->with(['campuses' => function ($query3) {
                                $query3->select('campuses.id', 'campuses.code');
                            }]);
                        }]);
                    }]);
                },
            ]);

            return response()->json(array(
                'error' => false,
                'data' => $demoCustomer,
                'message' => 'Thêm khách hàng Demo thành công!',
            ));
        }, 'Thêm khách hàng Demo không thành công!');
    }

    public function updateCustomer(Request $request)
    {
        $demoCustomer = DemoCustomer::find($request->id);
        if (!$demoCustomer) {
            return response()->json(array(
                'error' => true,
                'message' => 'Không tìm thấy khách hàng trong chương trình Demo!',
            ));
        } 

        return $this->handleTransaction(function () use ($request, $demoCustomer) {
            $data = []; 
          
            if ($request->filled('join')) { 
                $data['join'] = $request->join;
            }
            if ($request->filled('sign')) {
                $data['sign'] = $request->sign;
            }
            if ($request->filled('comment')) {
                $data['comment'] = $request->comment;
            }
             
            $demoCustomer->update($data); 

            return response()->json(array(
                'error' => false,
                'message' => 'Cập nhật khách hàng Demo thành công!',
            ));
        }, 'Cập nhật khách hàng Demo không thành công!');
    }

    public function removeCustomer(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            DemoCustomer::find($request->id)->delete();

            return response()->json(array(
                'error' => false,
                'message' => 'Xóa khách hàng Demo thành công!',
            ));
        }, 'Xóa khách hàng Demo không thành công!');
    }
}
