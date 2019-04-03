<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','bio','photo','type','api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function resize_300_200($photo)
    {
        $arr=explode(".",$photo);
        $arr[0]=$arr[0].'_300_200';
        $photo_resize=implode(".",$arr);
        return $photo_resize;
    }

    public function resize()
    {
        $arr=explode(".",$this->photo);
        $arr[0]=$arr[0].'_300_200';
        $photo_resize=implode(".",$arr);
        return $photo_resize;
    }

    public function isAdminAuthor()
    {
        if($this->type==='admin' || $this->type==='author'){
            return true;
        }
    }
}
