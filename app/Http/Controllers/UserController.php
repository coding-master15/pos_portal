<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use View;
use Form;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $b = datatables()->of(Admin::query())->toJson();
        $a = explode("{", $b, 2);
            $c = sizeof($a) == 1 ? json_encode([]) : '{'.$a[1];
        $data = [
            'category_name' => 'userslist',
            'page_name' => 'userslist',
            'has_scrollspy' => 1,
            'scrollspy_offset' => 140,
            //'users' => json_decode($c)

        ];
        return View::make('pages.tables.users_table')->with($data);
    }

    public function getUser($request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Admin::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Admin::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

        // Fetch records
        $records = Admin::orderBy($columnName,$columnSortOrder)
            ->where('admins.name', 'like', '%' .$searchValue . '%')
            ->select('admins.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $record->id;
            $name = $record->name;
            $image = $record->image;
            $email = $record->email;
            $phone = $record->phone;
            $shop_name = $record->shop_name;
            $plan = $record->plan;
            $status = $record->status;
            $plan_expiry_date = $record->plan_expiry_date;
            $created_at = $record->created_at;

            $model = Form::open(array('url' => 'api/update_plan', 'method' => 'post')).'<div class="modal fade" id="exampleModalCenter'.$id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle'.$id.'" aria-hidden="true"> <div class="modal-dialog modal-dialog-centered" role="document"> <div class="modal-content"> <div class="modal-header"> <h5 class="modal-title" id="exampleModalCenterTitle'.$id.'">Update Plan</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> </div> <div class="modal-body"> <div class="form-group"> <label for="exampleSelect">Select Plan</label> <select class="form-control" name="plan" id="exampleSelect"> <option value="free">Free</option> <option value="paid">Paid</option> </select> </div> <input type="hidden" id="submitPlan'.$id.'hidden" value="'.$id.'" name="id"> <div class="form-group"> <label for="exampleSelect">Select Expiry Date</label> <input class="form-control expiry" id="expirySelect'.$id.'" name="expiry" type="date"/> </div> <script> var myDate = new Date("'.$plan_expiry_date.'"); document.getElementById("expirySelect'.$id.'").value = myDate.toISOString().substring(0, 10);; </script> </div> <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> <button id="submitPlan'.$id.'" class="submitPlan btn btn-primary" type="submit">Save changes</button> </div> </div> </div> </div>'.Form::close().Form::open(array('url' => 'api/block_user', 'method' => 'post')).'<div class="modal fade" id="exampleModalCenter2'.$id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2'.$id.'" aria-hidden="true"> <div class="modal-dialog modal-dialog-centered" role="document"> <input type="hidden" value="'.$id.'" name="id"> <div class="modal-content"> <div class="modal-header"> <h5 class="modal-title" id="exampleModalCenterTitle2'.$id.'">Update Plan</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> </div> <div class="modal-body"> Are you sure want to '.($status == 'active' ? 'Block' : 'Unblock').'  the User? </div> <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> <button id="submitPlan'.$id.'" class="submitPlan btn btn-warning" type="submit">'.($status == 'active' ? 'Block' : 'Unblock').'</button> </div> </div> </div> </div>'.Form::close();

            $data_arr[] = array(
                "id" => $id,
                "image" => '<img src="'.($image ?? '/storage/img/avatar.png').'"  width="60px"/>',
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "shop_name" => $shop_name,
                "plan" => $plan,
                "status" => $status,
                "plan_expiry_date" => date('d-M-Y', strtotime($plan_expiry_date)),
                "created_at" => date('d-M-Y', strtotime($created_at)),
                "action" => '<td class="text-center"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter'.$id.'"> Plan </button></td><td class="text-center"><button type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalCenter2'.$id.'">'.($status == 'active' ? 'Block' : 'Unblock').'</button></td>'.$model
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ); 

        echo json_encode($response);
        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $user)
    {
        //
    }

    public static function updatePlan($request) {
        $id = $request->input('id');
        $plan = $request->input('plan');
        $expiry = $request->input('expiry');
        $user = Admin::find($id);
        $user->plan = $plan;
        $user->plan_expiry_date = $expiry;
        $user->save();
        return redirect()->back()->with('message', 'User Updated!');
    }

    public static function blockUser($request) {
        $id = $request->input('id');
        $user = Admin::find($id);
        $user->status = $user->status == 'active' ? 'inactive' : 'active';
        $user->save();
        return redirect()->back()->with('message', 'User Updated!');
    }
}
