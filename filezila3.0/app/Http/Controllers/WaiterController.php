<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\PositionFirstResource;
use App\Http\Resources\ShiftFindResource;
use App\Models\Order;
use App\Models\Position;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;

class WaiterController extends Controller
{
    public function order_create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_shift_id' => 'required',
            'table_id' => 'required',
            'number_of_person' => 'nullable|integer',
        ]);

        $validator->setCustomMessages([
            'required' => 'field :attribute can not be blank',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $shift = DB::table('shifts')->where('id', '=', $request->input('work_shift_id'))->first();

        if ($shift->active !== 1) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. The shift must be active!'
            ], 403);
        }

        $user = Auth::user();
        $user_id = $user->id;


        $shift_worker = DB::table('worker_shifts')->where('shift_workers_id', '=', $user_id)->first();


        if (!$shift_worker) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. You dont work this shift!'
            ], 403);
        }

        $order = Order::create([
            'table_id' => $request->input('table_id'),
            'shift_workers' => $user_id,
            'create_at' => now()->format('Y-m-d H:i'),
            'status' => 'Принят',
            'price' => 0
        ]);

        return response()->json([
            'data' => [
                'id' => $order->id,
                'table' => 'Столик №' . $order->table_id,
                'shift_workers' => $order->shift_workers,
                'create_at' => $order->create_at,
                'status' => $order->status,
                'price' => $order->price
            ]
        ]);
    }

    public function order_find($id)
    {

        $user = Auth::user();
        $user_id = $user->id;

        $shift = DB::table('worker_shifts')->where('shift_workers_id', '=', $user_id)->first();

        if (!$shift) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. You did not accept this order!'
            ], 403);
        }

        return response()->json(new OrderResource(Shift::find($id)));
    }

    public function order_all($id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $shift = DB::table('worker_shifts')->where('shift_workers_id', '=', $user_id)->first();

        if (!$shift) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. You did not accept this order!'
            ], 403);
        }

        return response()->json(OrderResource::collection(Shift::all()));
    }

    public function order_position(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => 'required',
            'count' => 'required|min:1|max:10',
        ]);

        $validator->setCustomMessages([
            'required' => 'field :attribute can not be blank',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $user_id = $user->id;

        $shift_active = DB::table('shifts')->where('id', '=', $id)->first();


        if(!($shift_active->active === 0)) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden! You cannot change the order status of a closed shift!'
            ], 403);
        }


        $shift = DB::table('orders')->where('shift_workers', '=', $user_id)->first();


        if(!$shift) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden! You cannot change the order status of a closed shift!'
            ], 403);
        }


        Position::create([
            'menu_id' => $request->input('menu_id'),
            'count' => $request->input('count')
        ]);

        return response()->json(PositionFirstResource::collection(Order::all()));
    }

    public function order_position_delete() {


        return response()->json(PositionFirstResource::collection(Order::all()));
    }

}
