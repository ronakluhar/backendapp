<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\User;
use Redirect;
use Illuminate\Validation\Rule;
use Image;
use Config;
use File;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->userOriginalImageUploadPath = Config::get('constant.USER_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->userThumbImageUploadPath = Config::get('constant.USER_THUMB_IMAGE_UPLOAD_PATH');
        $this->userThumbImageHeight = Config::get('constant.USER_THUMB_IMAGE_HEIGHT');
        $this->userThumbImageWidth = Config::get('constant.USER_THUMB_IMAGE_WIDTH');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addUser()
    {      
        $uploadUserThumbPath = $this->userThumbImageUploadPath;
        return view('admin.add-user',compact('uploadUserThumbPath'));
    }
    
    /*save user in data*/
    public function saveUser()
    {
        $this->validate(request(),[
           'name' => 'required', 
           'email' => ['required','email',Rule::unique('users','email')->ignore(Input::get('id'))],
           'phone' => 'required'
        ]); 
        
        $user = new User();
        $postData = Input::all();   
        
      
        $hiddenProfile = Input::get('hidden_profile');
        $postData['photo'] = $hiddenProfile;
        if (Input::file()) {
            $file = Input::file('photo');
            
            if (!empty($file)) {   
                $ext = $file->getClientOriginalExtension();
                $validImageExtArr = array('jpg', 'jpeg', 'png', 'bmp', 'PNG');
                
                if (in_array($ext, $validImageExtArr)) {                
                    $fileName = 'user_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->userOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->userThumbImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->userThumbImageWidth, $this->userThumbImageHeight)->save($pathThumb);  

                    if ($hiddenProfile != '' && $hiddenProfile != "default.png") {
                        $imageOriginal = public_path($this->userOriginalImageUploadPath . $hiddenProfile);
                        $imageThumb = public_path($this->userThumbImageUploadPath . $hiddenProfile);
                        if(file_exists($imageOriginal) && $hiddenProfile != ''){File::delete($imageOriginal);}
                        if(file_exists($imageThumb) && $hiddenProfile != ''){File::delete($imageThumb);}
                    }                    
                    $postData['photo'] = $fileName;         
                }
            }
        }
        
        if(isset($postData['id']) && $postData['id'] > 0)
        {
           $user = User::find($postData['id']);
           $user->name = $postData['name'];
           $user->email = $postData['email'];
           $user->phone = $postData['phone'];
           $user->photo = $postData['photo'];
           $user->save();
           return Redirect::to("/admin/users/")->with('success', 'User has been updated successfully');
           exit;
        }else{
           $postData['password'] = bcrypt($postData['password']); 
           $user::create($postData);  
           return Redirect::to("/admin/users/")->with('success', 'User has been created successfully');
           exit;
        }
        
        
    }
    
    public function getUser()
    {        
        $uploadUserThumbPath = $this->userThumbImageUploadPath;
        $users = User::where('is_admin',0)->get();
        return view('admin.user-list',compact('users','uploadUserThumbPath'));
    }
    
    public function editUser($id)
    {
        $user = User::find($id);
        $uploadUserThumbPath = $this->userThumbImageUploadPath;        
        return view('admin.add-user',compact('user','uploadUserThumbPath'));
    }
    
    public function deleteUser($id)
    {
        $user = User::find($id);
        $user->delete();
        return Redirect::to("/admin/users/")->with('success', 'User has been deleted successfully');
        exit;
    }
}
