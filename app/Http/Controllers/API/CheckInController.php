<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Guest;
use App\Models\Room;
use App\Models\TemporaryCheckInKiosk;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
public function store(Request $request)
{

        // $user = Auth::user();

         $room = Room::where('branch_id', $request->branch_id)
                    ->where('id', $request->room_id)
                    ->where('status', 'Occupied')
                    ->with('latestCheckInDetail')
                    ->lockForUpdate()
                    ->first();

                if ($room) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'This room is already occupied.',
                    ], 400);
                }

                $temporaryCheckInKiosk = TemporaryCheckInKiosk::where('branch_id', $request->branch_id)
                    ->where('room_id', $request->room_id)
                    ->lockForUpdate()
                    ->exists();

                $temporaryReserved = TemporaryReserved::where('branch_id', $request->branch_id)
                    ->where('room_id', $request->room_id)
                    ->lockForUpdate()
                    ->exists();

                if ($temporaryCheckInKiosk || $temporaryReserved) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Room is already reserved. Please select another room.',
                    ], 400);
                }       

        $transaction = Guest::whereYear('created_at', Carbon::today()->year)->count() + 1;

        $transaction_code = $request->branch_id . today()->format('y') . str_pad($transaction, 4, '0', STR_PAD_LEFT);

             $guest = Guest::create([
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'contact' => $request->contact == null ? 'N/A' : "09{$request->contact}",
            'qr_code' => $transaction_code,
            'room_id' => $request->room_id,
            'rate_id' => $request->rate_id,
            'type_id' => $request->type_id,
            'static_amount' => $request->room_pay,
            'is_long_stay' => $request->longstay > 0 ? true : false,
            'number_of_days' => $request->longstay ?? 0,
            'has_discount' => $request->has_discount,
            'discount_amount' => $request->discount_amount ?? 0,
            ]);


            TemporaryCheckInKiosk::create([
                'guest_id' => $guest->id,
                'room_id' => $request->room_id,
                'branch_id' => $request->branch_id,
                'terminated_at' => Carbon::now()->addMinutes(20),
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Guest successfully checked in.',
                'guest' => $guest,
            ], 201);


    }
}
