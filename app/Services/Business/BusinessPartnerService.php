<?php
namespace App\Services\Business;

use App\Models\BusinessPartnerStatus;
use App\Models\BusinessPartnerVolumn;

use Illuminate\Support\Carbon;

class BusinessPartnerService
{
    public function updateStatus($partnerId, $amountContract = 0)
    {
        $record = BusinessPartnerStatus::where('partner_id', $partnerId)->whereDate('created_at',Carbon::now()->format('Y-m-d'))->first();
        
        if ($record) {
            $record->amount_contract += $amountContract;
            $record->customers += 1;
            $record->save();
        } else {
            BusinessPartnerStatus::create([
                'customers' => 1,
                'partner_id' => $partnerId,
                'amount_contract' => $amountContract
            ]);
        }
        
        return;
    }

    public function updateTopHocVienThiTruong() {
        $market_id = request()->input('market_id');

        $record = BusinessPartnerStatus::where('market_id', $market_id)->whereDate('created_at',Carbon::now()->format('Y-m-d'))->first();
    }
}