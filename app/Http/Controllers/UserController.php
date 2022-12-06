<?php
declare(strict_types=1);
namespace APP\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User as UserModel;
use App\Http\Requests\UserRegisterPost;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class UserController extends Controller
{
    /**
     * トップページ を表示する
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('/user/register');
    }
    
    /**
     * 会員登録処理
     * 
     */
    public function register(UserRegisterPost $request)
    {
        // validat済
        $datum = $request->validated();
        $datum['password'] = Hash::make($datum['password']);
       
        // テーブルへのINSERT
        try {
            $r = UserModel::create($datum);
    } catch(\Throwable $e) {
        // XXX 本当はログに書く等の処理をする。今回は一端「出力する」だけ
        echo $e->getMessage();
        exit;
    }

    // ユーザの登録成功
    $request->session()->flash('front.user_register_success', true);
    
    //
    $request->session()->regenerate();
    //
    return redirect('/');
    }
}