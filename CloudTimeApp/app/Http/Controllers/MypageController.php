<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Capsule;
use App\Models\User_info;
use App\Models\Bbs;
use App\Models\Member;
use Auth;
use strcmp;
use Illuminate\Support\Facades\Storage;
use Validator;

class MypageController extends Controller{
    // プロフィール表示画面
    public function show_info($id=0){
        $i_am = Auth::id();
        if($id == 0){
            $user = User::find($i_am);
        }else if($id > 0){
            $user = User::find($id);
        }

        // 開くページが自身のページかを判定
        $i_am_flag = $this -> i_am_flag_system($i_am,$id);

        // 誕生日タイプ取得
        $user->birth_type = $this -> get_birth_type_system($user->birthday);

        $data = ['user_data' => $user, 'i_am_flag' => $i_am_flag];

        return view('mypage.mypage_info',$data);
    }

    // プロフィール編集画面
    public function show_edit($id=0){
        $i_am = Auth::id();
        $user = User::find($i_am);

        // 誕生日タイプ取得
        $user->birth_type = $this -> get_birth_type_system($user->birthday);

        $data = ['user_data' => $user];

        return view('mypage.mypage_edit',$data);
    }

    // ユーザーテーブル更新処理
    public function mypage_update(Request $req){
        $i_am = Auth::id();
        $user = User::find($i_am);
        $data = ['user_data' => $user];
        $rulus = [
        
            'name' => 'required',
        ];

        $message = [
           
            'name.required' => '名前を入力してください',
        ];

        $validator = Validator::make($req->all(), $rulus, $message);

        if($validator->fails()){
            return view('mypage.mypage_edit',$data)
            ->withErrors($validator);
        }
        $this -> user_update_system($i_am,$req);
        return redirect()->route('mypage.show_info', ['user_id' => $i_am])->with('message','プロフィールを更新しました。');
    }

    private function user_update_system($user_id,$req){
        $user = User::find($user_id);
        if($req->image != null){
            if( strcmp( $user->profile_pic, "0" ) != 0 ){
                $delete_path = basename($user->profile_pic);
                $delete_path = str_replace('https://example.s3-ap-northeast-1.amazonaws.com/', '', $delete_path);
                $disk = Storage::disk('s3');
                $disk->delete($delete_path);
            };
            $uploadImg = $user -> profile_pic = $req->file('image');
            $path = Storage::disk('s3')->putFile('/', $uploadImg, 'public');
            $user->profile_pic = Storage::disk('s3')->url($path);
        };
        $user -> name = $req -> name;
        $user -> birthday = $req -> birthday;
        $user -> intro = $req -> intro;
        $user -> location = $req -> location;
        $user -> job = $req -> job;
        $user -> high = $req -> high;
        $user -> junior_high = $req -> junior_high;
        $user -> elementary = $req -> elementary;
        $user -> save();
    }

    // 誕生月に応じたプロフィールページの背景設定用の値取得 (春:1,夏:2,秋:3,冬:4)
    private function get_birth_type_system($birth_data){
        $ret_data = 0;
        $birth_month_str = substr($birth_data,5,2);
        $birth_month_int = (int)$birth_month_str;
        if($birth_month_int == 12 || $birth_month_int <= 2){
            $ret_data = 4;
        }else if($birth_month_int >= 9){
            $ret_data = 3;
        }else if($birth_month_int >= 6){
            $ret_data = 2;
        }else if($birth_month_int >= 3){
            $ret_data = 1;
        }
        return $ret_data;
    }

    private function i_am_flag_system($my_id,$url_id){
        $ret_data = 0;
        if($my_id == $url_id or 0 == $url_id){
            $ret_data = 1;
        }
        return $ret_data;
    }
}