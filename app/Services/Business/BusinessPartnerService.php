<?php
namespace App\Services\Business;

use App\Models\BusinessPartnerStatus;
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
}