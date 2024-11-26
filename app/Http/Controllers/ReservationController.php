<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Meal;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $table_id = $request->table_id;
        $fromTime = $request->from_time;
        $toTime = $request->to_time;
        $guestCount = $request->gest_count;
        $avilableTables = Table::where('id', $table_id)
        ->where('capacity', '>=', $guestCount)
        ->whereDoesntHave('reservations', function ($query) use ($fromTime, $toTime) {
            $query->where('from_time', '<', $toTime)
                ->where('to_time', '>', $fromTime);
        })->get();

        if ($avilableTables->isNotEmpty()) {
            Reservation::create([
                'table_id' => $table_id,
                'customer_id' => $request->customer_id,
                'from_time' => $fromTime,
                'to_time' => $toTime
            ]);
            return response()->json(['Message' => 'Your request has been done']);
        } else {
            return response()->json(['Message' => 'The table is not available for the given time']);
        }
    }
    public function mealsAccordingQunatity(){
        $limitedMeals = 80;
        $meals = Meal::where('quantity_available','>',$limitedMeals)->get();
        if ($meals->isEmpty()) {
            return response()->json(['Meals' => 'Sold Out']);
        }
        return response()->json(['Meals' => $meals]);
    }
    public function orders(Request $request)
    {
        $meals = Meal::all();
        $grandTotal = 0;
        $orderDetails = [];

        foreach ($meals as $meal) {
            $total = $meal->price - $meal->discount;
            $grandTotal += $total;

            $orderDetails[] = [
                'meal_id' => $meal->id,
                'amount_to_pay' => $total
            ];
        }

        $order = Order::create([
            'table_id' => $request->table_id,
            'reservation_id' => $request->reservation_id,
            'customer_id' => $request->customer_id,
            'total' => $grandTotal,
            'paid' => 'pending',
            'date' => now()
        ]);

        foreach ($orderDetails as $detail) {
            $order->meals()->attach($detail['meal_id'], ['amount_to_pay' => $detail['amount_to_pay']]);
        }
        return response()->json(['order' => $order, 'details' => $order->meals]);
    }

    public function checkout(Request $request){
        $order = Order::where('table_id',$request->table_id)
        ->where('reservation_id',$request->reservation_id)
        ->first();
        if ($order) {
            if ($order->paid == 'done') {
                return response()->json(['message' => 'The table has already been checked out.']);
            }
            $order->update([
                'paid' => 'done',
            ]);
            return response()->json(['message' => 'Checkout successful.']);
        }
        return response()->json(['message' => 'Order not found.']);
    }
}

// $toTime = Reservation::whereTime('to_time',$request->time);
