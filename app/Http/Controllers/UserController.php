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
            'users' => json_decode($c)

        ];
        return View::make('pages.tables.users_table')->with($data);
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
