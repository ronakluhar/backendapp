@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-9">
            <h2>Users List</h2>
        </div>
        <div class="col-md-3" style="float: right !important;">
            <a class="btn btn-primary" href="{{url('admin/add-user')}}">Add User</a>
        </div>               
    </div> 
   <div class="row">         
        <div class="col-md-12">
            <div class="box box-primary">
                
                <div class="box-body">
                     <table class="table table-striped">
                        <tr>
                            <th>Unique Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Photo</th>
                            <th>Active</th>
                        </tr>
                        @forelse($users as $user)
                         <tr>
                            <td>
                                {{$user->unique_id}}
                            </td>
                            <td>
                                {{$user->name}}
                            </td>
                            <td>
                                {{ $user->email }}
                            </td>
                            <td>
                                {{ $user->phone }}
                            </td>
                            <td>
                                <?php
                                   if (File::exists(public_path($uploadUserThumbPath . $user->photo)) && $user->photo != '') {
                                        ?>
                                        <img src="{{ url($uploadUserThumbPath.$user->photo) }}" alt="{{$user->photo}}"  height="50" width="50">
                                    <?php } else { ?>
                                        <img src="{{ asset('/uploads/user/thumb/default.png')}}" class="user-image" alt="Default Image" height="50" width="50">
                                        <?php
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="{{ url('/admin/edituser') }}/{{$user->id}}"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                                
                                <a onclick="return confirm('Are you sure you want to delete this record?')" href="{{ url('/admin/deleteuser') }}/{{$user->id}}"><i class="i_delete fa fa-trash"></i></a>
                            </td>
                         </tr>
                         @empty
                         <tr>
                            <td colspan="4"><center>No Record Found</center></td>
                         </tr>
                         @endforelse
                     </table>                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
