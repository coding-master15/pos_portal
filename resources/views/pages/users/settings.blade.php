@extends('layouts.app')

@section('content')

            <div class="layout-px-spacing">                
                    
            <form id="general-info" class="section general-info" action="/api/update_setting" method="POST">

                <div class="account-settings-container layout-top-spacing">

                    <div class="account-content">
                        <div class="scrollspy-example" data-spy="scroll" data-target="#account-settings-scroll" data-offset="-100">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                        <div class="info">
                                            <h6 class="">General Information</h6>
                                            <div class="row">
                                                <div class="col-md-11 mx-auto">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="address">Free Plan Days</label>
                                                                <input type="number" name="trial_days" class="form-control mb-4" id="address" placeholder="Free Plan Days" value="{{$setting->trial_days ?? 0}}" >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location">Support Email</label>
                                                                <input type="text" class="form-control mb-4" id="location" placeholder="Support Email" name="support_email" value="{{$setting->support_email ?? ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="phone">Support Phone</label>
                                                                <input type="text" class="form-control mb-4" id="phone" placeholder="Support Phone" name="support_phone" value="{{$setting->support_phone ?? ''}}">
                                                            </div>
                                                        </div>                                                                               
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="account-settings-footer">
                        
                        <div class="as-footer-container">
                            <span></span>
                            <div class="blockui-growl-message">
                                <i class="flaticon-double-check"></i>&nbsp; Settings Saved Successfully
                            </div>
                            <button id="multiple-messages" class="btn btn-dark">Save Changes</button>

                        </div>

                    </div>
                </div>
                </form>
            </div>
@endsection