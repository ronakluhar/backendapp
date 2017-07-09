@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Media</div>
                <div class="panel-body">
                    <form class="form-horizontal" id="mediaForm" enctype="multipart/form-data" method="POST" action="{{ url('admin/saveMedia') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="<?php echo (isset($media) && !empty($media)) ? $media->id : '0' ?>">
                        <input type="hidden" name="hidden_profile" value="<?php echo (isset($media) && !empty($media)) ? $media->media : '' ?>">                                                                  
                        
                        <div class="form-group">
                            <label for="media" class="col-md-4 control-label">Select File</label>
                            <div class="col-md-6">
                                <input type="file" id="media" name="media">  
                                <?php
                                if (isset($media->id) && $media->id != '0') {
                                    if (File::exists(public_path($uploadMediaOriginalPath . $media->media)) && $media->media != '') {
                                        ?>
                                        <a href="{{ url($uploadMediaOriginalPath.$media->media) }}">{{ url($uploadMediaOriginalPath.$media->media) }}</a>
                                    <?php } else { ?>
                                        <img src="{{ asset('/uploads/media/original/default.png')}}" class="user-image" alt="Default Image" height="70" width="70">
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        
                        <?php
                        if(isset($media) && !empty($media))
                        {
                            $message = $media->message;                                
                        }                                
                        else
                        {                                
                            $message = old('message');                                
                        }                                
                        ?>
                        
                        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                            <label for="message" class="col-md-4 control-label">Message</label>
                            <div class="col-md-6">
                                <textarea class="form-control" id="message" name="message">{{$message}}</textarea> 
                                @if ($errors->has('message'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('message') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                                                                                                                                                
                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>                                
                            </div>
                            <div class="col-md-3">
                                <a href="{{url('admin/media')}}" class="btn btn-primary">Cancel</a>
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
        var mediaRules = {            
            message: {
                required: true
            }
        };
        $("#mediaForm").validate({
            rules: mediaRules,
            messages: {                
                message: {
                    required: 'Enter message'
                }
            }
        });
    });
</script>
@endsection