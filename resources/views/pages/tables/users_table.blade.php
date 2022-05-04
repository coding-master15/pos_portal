@extends('layouts.app')

@section('content')
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">
                
                    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="table-responsive mb-4 mt-4">
                                <table id="zero-config" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Avatar</th>
                                            <th>Name</th>
                                            <th>Shop Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Plan</th>
                                            <th>Plan Expiry</th>
                                            <th>Registration Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($users->data) as $user)
                                        <tr>
                                           
                                            <td><img src='{{ $user->image ?? "/storage/img/avatar.png" }}' alt="avatar" width="60px"/></td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->shop_name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->plan }}</td>
                                            <td>{{ date('d-M-Y H:i:s', strtotime($user->plan_expiry_date)) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($user->created_at)) }}</td>
                                            <td class="text-center"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter{{$user->id}}">
                                            Plan
                                            </button></td>
                                            <td class="text-center"><button type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalCenter2{{$user->id}}">
                                            {{ $user->status == 'active' ? 'Block' : 'Unblock' }}
                                            </button></td>
                                            <td class="text-center"><button class="btn btn-danger">Delete</button> </td>
                                        </tr>
                                        {{ Form::open(array('url' => 'api/update_plan', 'method' => 'post')) }}
                                        <div class="modal fade" id="exampleModalCenter{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle{{$user->id}}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalCenterTitle{{$user->id}}">Update Plan</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                           
                                            <div class="form-group">
                                                <label for="exampleSelect">Select Plan</label>
                                                <select class="form-control" name="plan" id="exampleSelect">
                                                <option value="free">Free</option>
                                                <option value="paid">Paid</option>
                                                </select>
                                            </div>
                                            <input type="hidden" id="submitPlan{{$user->id}}hidden" value="{{$user->id}}" name="id">

                                            <div class="form-group">
                                                <label for="exampleSelect">Select Expiry Date</label>
                                                <input class="form-control expiry" id="expirySelect{{$user->id}}" name="expiry" type="date"/>
                                            </div>
                                            <script>
                                                var myDate = new Date('{{ $user->plan_expiry_date }}');
                                                document.getElementById('expirySelect{{$user->id}}').value = myDate.toISOString().substring(0, 10);;
                                                </script>
                                            
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button id='submitPlan{{$user->id}}' class="submitPlan btn btn-primary" type="submit">Save changes</button>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                        {{ Form::close() }}
                                        {{ Form::open(array('url' => 'api/block_user', 'method' => 'post')) }}
                                        <div class="modal fade" id="exampleModalCenter2{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2{{$user->id}}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                        <input type="hidden" value="{{$user->id}}" name="id">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalCenterTitle2{{$user->id}}">Update Plan</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                           
                                            Are you sure want to {{ $user->status == 'active' ? 'Block' : 'Unblock' }} the User?
                                            
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button id='submitPlan{{$user->id}}' class="submitPlan btn btn-warning" type="submit">{{ $user->status == 'active' ? 'Block' : 'Unblock' }}</button>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                        {{ Form::close() }}
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        <th>Avatar</th>
                                            <th>Name</th>
                                            <th>Shop Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Plan</th>
                                            <th>Plan Expiry</th>
                                            <th>Registration Date</th>
                                            <th class="invisible"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <script type="text/javascript">
            $('.submitPlan').click(function () {
                var plan = $('#'+this.id+' select');
                var expiry = $('#'+this.id+' input.expiry');
                var userId = $('#'+this.id+'hidden').val();
                console.log(userId);
            });
</script>
@endsection