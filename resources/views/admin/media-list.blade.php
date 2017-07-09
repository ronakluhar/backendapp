@extends('layouts.app')

@section('content')
<div class="container">
   
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-9">
            <h2>Media List</h2>
        </div>
        <div class="col-md-3" style="float: right !important;">
            <a class="btn btn-primary" href="{{url('admin/add-media')}}">Add Media</a>
        </div>               
    </div>
    
   <div class="row">         
        <div class="col-md-12">
            <div class="box box-primary">
                
                <div class="box-body">
                     <table class="table table-striped">
                        <tr>
                            <th>Message</th>
                            <th>Media</th>                            
                            <th>Action</th>
                        </tr>
                        @forelse($medias as $media)
                         <tr>
                            <td>
                                {{$media->message}}
                            </td>
                            <td>
                                <?php
                                   if (File::exists(public_path($uploadMediaOriginalPath . $media->media)) && $media->media != '') {
                                        ?>
                                <a href="{{ url($uploadMediaOriginalPath.$media->media) }}" target="_blank">{{ url($uploadMediaOriginalPath.$media->media) }}</a>
                                    <?php } else { ?>
                                        <img src="{{ asset('/uploads/media/original/default.png')}}" class="user-image" alt="Default Image" height="50" width="50">
                                        <?php
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="{{ url('/admin/editMedia') }}/{{$media->id}}"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;                                
                                <a onclick="return confirm('Are you sure you want to delete this record?')" href="{{ url('/admin/deleteMedia') }}/{{$media->id}}"><i class="i_delete fa fa-trash"></i></a>
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
