<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use View;
use Form;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'category_name' => 'settings',
            'page_name' => 'settings',
            'has_scrollspy' => 1,
            'scrollspy_offset' => 140,
            'setting' => \DB::table('settings')->where('id', 1)->first()

        ];
        return View::make('pages.users.settings')->with($data);
    }

    public static function updateSetting(Request $request) {
        $user = Setting::find(1);
        $user->trial_days = $request->trial_days;
        $user->support_email = $request->support_email;
        $user->support_phone = $request->support_phone;
        $user->save();
        return redirect()->back()->with('message', 'Settings Updated!');
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
