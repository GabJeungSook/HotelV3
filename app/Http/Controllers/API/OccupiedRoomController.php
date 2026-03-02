<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Floor;
use App\Models\CheckInDetail;
use App\Models\Guest;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Log;

class OccupiedRoomController extends Controller
{
    public function occupiedRooms($branchId)
    {
        try {
            //query
            $floors = Floor::where('branch_id', $branchId)
                ->with(['rooms' => function ($query) {
                    $query->where('status', 'Occupied')
                    ->with([
                        'latestCheckInDetail.guest.type',
                        'latestCheckInDetail.extendedGuestReports',
                    ])
                    ->withSum([
                        'latestCheckInDetail.extendedGuestReports as extension_hours' => function ($q) {},
                    ], 'total_hours')
                     ->withSum([
                        'latestCheckInDetail.transactions as room_amount' => function ($q) {
                        $q->whereNotIn('transaction_type_id', [1]);
                        },
                    ], 'payable_amount')
                    ->withSum([
                        'latestCheckInDetail.transactions as total_amount' => function ($q) {
                        $q->whereNotIn('transaction_type_id', [1,2,5]);
                        },
                    ], 'payable_amount')
                    ->withSum([
                        'latestCheckInDetail.transactions as total_deposit' => function ($q) {
                        $q->where('transaction_type_id', 2);
                        },
                    ], 'payable_amount');
                }])
                ->orderBy('number')
                ->get();
            // $floors = Floor::where('branch_id', $branchId)->with(['rooms' => function ($query) use ($branchId) {
            //         $query->where('status', 'Occupied')->with(['latestCheckInDetail.guest.type', 'latestCheckInDetail.transactions' => function ($q) {
            //             $q->whereNotIn('transaction_type_id', [2,5]);
            //         }, 'latestCheckInDetail.extendedGuestReports']);
            //     }])
            //     ->orderBy('number')
            //     ->get();

            return ApiResponse::success(['data' => $floors], 200);
        } catch (\Exception $e) {
            Log::error('Occupied Rooms API Error: ' . $e->getMessage(), [
                'trace' => $e->getTrace()
            ]);
            return ApiResponse::error($e->getMessage());
        }
    }


}
