@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add User</div>
                <div class="panel-body">
                    <form class="form-horizontal" id="registerForm" enctype="multipart/form-data" method="POST" action="{{ url('admin/saveUser') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="<?php echo (isset($user) && !empty($user)) ? $user->id : '0' ?>">
                        <input type="hidden" name="hidden_profile" value="<?php echo (isset($user) && !empty($user)) ? $user->photo : '' ?>">                    
                        <div class="form-group{{ $errors->has('unique_id') ? ' has-error' : '' }}">
                            <label for="unique_id" class="col-md-4 control-label">Unique Id</label>
                            <?php
                            if(isset($user) && !empty($user))
                            {
                                $uniqueId = $user->unique_id;
                                $name = $user->name;
                                $email = $user->email;
                                $phone = $user->phone;
                            }                                
                            else
                            {
                                $uniqueId = str_random(20);
                                $name = old('name');
                                $email = old('email');
                                $phone = old('phone');
                            }                                
                            ?>                            
                            <div class="col-md-6">
                                <input id="name" readonly="true" type="text" class="form-control" name="unique_id" value="{{ $uniqueId }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('unique_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $name }}" autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="photo" class="col-md-4 control-label">Photo</label>
                            <div class="col-md-6">
                                <input type="file" id="photo" name="photo">   
                                <?php
                                if (isset($user->id) && $user->id != '0') {
                                    if (File::exists(public_path($uploadUserThumbPath . $user->photo)) && $user->photo != '') {
                                        ?>
                                        <img src="{{ url($uploadUserThumbPath.$user->photo) }}" alt="{{$user->photo}}"  height="70" width="70">
                                    <?php } else { ?>
                                        <img src="{{ asset('/uploads/user/thumb/default.png')}}" class="user-image" alt="Default Image" height="70" width="70">
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="{{ $email }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="col-md-4 control-label">Phone</label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control" name="phone" value="{{ $phone }}" autofocus>

                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <?php if(empty($user)) { ?>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <?php } ?>
                        
                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                                
                            </div>
                            <div class="col-md-3">
                                <a href="{{url('admin/users')}}" class="btn btn-primary">Cancel</a>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    jQuery(document).ready(function() {
        var registerRules = {
            email: {
                required: true,
                email  : true
            },
            name:{
              required:true  
            },
            phone:{
              required:true  
            },
            password: {
                required: true
            }
        };
        $("#registerForm").validate({
            rules: registerRules,
            messages: {
                email: {
                    required: 'Enter valid email'
                },
                password: {
                    required: 'Enter password'
                },
                name: {
                    required: 'Enter name'
                },
                phone: {
                    required: 'Enter phone'
                }
            }
        });
    });
</script>
@endsection