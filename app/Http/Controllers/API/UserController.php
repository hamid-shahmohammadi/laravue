<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $this->authorize('isAdmin');
        if(Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            return User::latest()->paginate(1);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'email'=>'required|string|email|max:191|unique:users',
            'password'=>'required|string|min:8|max:191'
        ]);
        return User::create([
            'name'=>$request['name'],
            'email'=>$request['email'],
            'type'=>$request['type'],
            'bio'=>$request['bio'],
            'photo'=>'profile.png',
            'password'=>Hash::make($request['password']),
            'api_token' => Str::random(60),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    public function profile()
    {
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        $user=auth('api')->user();
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'email'=>'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'=>'sometimes|min:8'
        ]);
        $currentPhoto=$user->photo;
        if($currentPhoto!=$request->photo && $request->has('photo')){

            $ext=explode('/',explode(':',substr($request->photo,0
                    ,strpos($request->photo,';')))[1])[1];

            $name=time().'_'.uniqid();

            $name_orginal=$name.'.'.$ext;

            $name_resize=$name.'_300_200.'.$ext;

            Image::make($request->photo)->save(public_path('img/profile/').$name_orginal);

            $image = Image::make($request->photo)->resize(300, 200);

            $image->save(public_path('img/profile/').$name_resize);

            $request->merge(['photo'=>$name_orginal]);

            $userphoto=public_path('img/profile/'.$currentPhoto);
            if(file_exists($userphoto)){
                @unlink($userphoto);
            }
            $userPhotoResize=User::resize_300_200($currentPhoto);
            $userphoto_300_200=public_path('img/profile/'.$userPhotoResize);
            if(file_exists($userphoto_300_200)){
                @unlink($userphoto_300_200);
            }
        }
        if($request->has('password')){
            $request->merge(['password'=>Hash::make($request['password'])]);
        }
        $user->update($request->all());
        return ['message'=>'Image Upload Success'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=User::findOrFail($id);
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'email'=>'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'=>'sometimes|min:8'
        ]);
        $user->name=$request['name'];
        $user->email=$request['email'];
        $user->type=$request['type'];
        $user->bio=$request['bio'];
        $user->api_token=Str::random(60);
        if($request->has('password')){
            $user->password=Hash::make($request['password']);
        }
        $user->save();
        return ['message'=>'updated the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::findOrFail($id);
        $user->delete();
        return ['message'=>'User Deleted'];
    }

    public function search(Request $request)
    {
        if($search=$request->input('q')){
            $users=User::where(function ($query) use ($search){
               $query->where('name','LIKE',"%$search%")
                   ->Orwhere('email','LIKE',"%$search%")
                   ->Orwhere('type','LIKE',"%$search%");
            })->paginate(10);
        }else{
            $users=User::latest()->paginate(10);
        }
        return $users;
    }
}
