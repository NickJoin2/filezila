<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminAllResource;
use App\Models\Order;
use App\Models\Shift;
use App\Models\ShiftWorker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    public function read()
    {
        return response()->json([
            'data' => AdminAllResource::collection(User::all())
        ]);
    }

    public function user_one($id) {
        $user = DB::table('users')->where('id', '=', $id)->first();
        return response()->json([
            'data' => $user
        ]);
    }

//    public function create(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'name' => 'required|string',
//            'surname' => 'nullable|string',
//            'patronymic' => 'nullable|string',
//            'login' => 'required|string|unique:users,login',
//            'password' => 'required|string',
//            'photo_file' => 'required',
//            'role_id' => 'required|numeric',
//        ]);
//
//        $validator->setCustomMessages([
//            'required' => 'field :attribute can not be blank',
//            'string' => 'field :attribute can not be blank',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'error' => 422,
//                'message' => 'Validation error',
//                'errors' => $validator->errors()
//            ], 422);
//        }
//
//        $base64Image = $request->input('photo_file');
//        $imageData = base64_decode($base64Image);
//
//        $fileName = 'photo_' . uniqid() . '.jpg'; // Generate a unique file name
//        $filePath = public_path('photos/' . $fileName);
//
//        file_put_contents($filePath, $imageData);
//
//        $user = User::create([
//            'name' => $request->input('name'),
//            'surname' => $request->input('surname'),
//            'patronymic' => $request->input('patronymic'),
//            'login' => $request->input('login'),
//            'password' => $request->input('password'),
//            'photo_file' => $fileName,
//            'role_id' => $request->input('role_id'),
//        ]);
//
//        return response()->json([
//            'data' => [
//                'id' => $user->id,
//                'status' => 'created'
//            ]
//        ]);
//    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'nullable|string',
            'patronymic' => 'nullable|string',
            'login' => 'required|string|unique:users,login',
            'password' => 'required|string',
            'photo_file' => 'required|mimes:jpeg,png',
            'role_id' => 'required|numeric',
        ]);

        $validator->setCustomMessages([
            'required' => 'field :attribute can not be blank',
            'string' => 'field :attribute can not be blank',
            'mimes' => 'field :attribute can only be of type jpeg or png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('photo_file');
        $store = $file->storeAs('photos', $file->getClientOriginalName());
        $path = storage_path('app/' . $file->getClientOriginalName());

        $user = User::create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'patronymic' => $request->input('patronymic'),
            'login' => $request->input('login'),
            'password' => $request->input('password'),
            'photo_file' => $path,
            'role_id' => $request->input('role_id'),
        ]);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'status' => 'created'
            ]
        ]);
    }

    public function work_shift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date_format:Y-m-d H:i|after:now',
            'end' => 'required|date_format:Y-m-d H:i|after:start',

        ]);

        $validator->setCustomMessages([
            'required' => 'field :attribute can not be blank',
            'after' => 'field :attribute can not be blank',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 422,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $shift = Shift::create([
            'start' => $request->input('start'),
            'end' => $request->input('end'),
        ]);

        return response()->json([
            'id' => $shift->id,
            'start' => $shift->start,
            'end' => $shift->end
        ]);
    }

    public function work_shift_open($id)
    {
        $shift = DB::table('shifts')->where('active', '=', 1)->count();

        if ($shift === 0) {
            DB::table('shifts')->where('id', '=', $id)->update(['active' => true]);
            $return = DB::table('shifts')->where('id', '=', $id)->get();

            return response()->json([
                'data' => $return
            ]);
        } else {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. There are open shifts!'
            ], 403);
        }
    }

    public function work_shift_close($id)
    {
        $shift = DB::table('shifts')->where('id', '=', $id)->first();

        $active = $shift->active;

        if ($active == 0) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. The shift is already closed!'
            ], 403);
        } else {
            $close = DB::table('shifts')->where('id', '=', $id)->update(['active' => 0]);
            return response()->json([
                'data' => $shift
            ]);
        }


    }

    public function work_shift_user(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
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

        $user = DB::table('worker_shifts')->where('shift_workers_id', '=', $request->input('user_id'))->count();
        if ($user === 1) {
            return response()->json([
                'error' => 403,
                'message' => 'Forbidden. The worker is already on shift!'
            ], 403);
        }

        $shift_worker = ShiftWorker::create([
            'shift_id' => $id,
            'shift_workers_id' => $request->input('user_id')
        ]);

        return response()->json([
            'data' => [
                'id_user' => $shift_worker->id,
                'status' => 'added'
            ]
        ]);
    }


}
