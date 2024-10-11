<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;

use App\Models\Customer\Customer;
use App\Models\Customer\ChangeManager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Constants\Customer\Active;
use App\Constants\Customer\Type;

class ChangeManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = ChangeManager::with([
            'customer',
            'new_user',
            'old_user',
        ]);

        if ($request->has('telephone') && $request->telephone) {
            $query->whereHas('customer', function ($query) use ($request) {
                $query->where('phone', $request->telephone);
            });
        }

        if ($request->has('created_at') && $request->created_at) {
            $query->whereDate('created_at', Carbon::parse($request->created_at)->format('Y-m-d'));
        }

        if ($request->has('status') && $request->status != null) {
            $query->whereDate('status', $request->status);
        }

        $change = $query->paginate(20);

        $totalPages = $change->lastPage();
        $data = $change->items();

        foreach ($data as $item) {
            $item->customer->consulting_detail =  json_decode($item->customer->consulting_detail);
        }

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
        $user = Auth::user();
        $customer = Customer::where('id', $request->customer_id)->first();

        if (!$customer || $customer->manage_id == $user->id || $customer->type != Type::DEPOT) {
            return response()->json(array(
                'error' => true,
                'message' => 'Không tìm thấy khách hàng hoặc bạn không có quyền thay đổi quản lý khách hàng này!',
                'data' => []
            ));
        }

        $changeManagement = ChangeManager::
            where('customer_id', $request->customer_id) 
            ->where('new_user_id', $user->id)
            ->where('status', 0)
            ->first();

        if ($changeManagement) {
            return response()->json(array(
                'error' => true,
                'message' => 'Bạn đã đề xuất thay đổi quản lý khách hàng này!',
                'data' => []
            ));
        }

        return $this->handleTransaction(function() use ($request, $user, $customer) {
            $result = ChangeManager::create([
                'customer_id' => $request->customer_id,
                'new_user_id' => $user->id,
                'old_user_id' => $customer->manage_id,
                'reason' => $request->reason,
                'status' => 0,
            ]);

            DB::commit();
            return response()->json(array(
                'error' => false,
                'data' => $result,
                'message' => 'Đề xuất thay đổi thành công!',
            ));
        }, 'Đề xuất thay đổi không thành công!');
    }

    public function update(Request $request)
    {

    }
}
