<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{

    public function getLogin() {
        return view('login');
    }
    public function postLogin(Request $request) {
        $rules = [
            'email_name' =>'required|email',
            'password_name' => 'required|min:8'
        ];
        $messages = [
            'email_name.required' => 'Email là trường bắt buộc',
            'email_name.email' => 'Email không đúng định dạng',
            'password_name.required' => 'Mật khẩu là trường bắt buộc',
            'password_name.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $email = $request->input('email_name');
            $password = $request->input('password_name');

            if( Auth::attempt(['email' => $email, 'password' =>$password])) {
                if(Auth::user()->confirmed==0){
                    $errors1 = new MessageBag(['errorlogin' => 'Chua xac thuc']);
                    return redirect()->back()->withInput()->withErrors($errors1);
                }
                return redirect()->intended('');
            } else {
                $errors1 = new MessageBag(['errorlogin' => 'Email hoặc mật khẩu không đúng']);
                return redirect()->back()->withInput()->withErrors($errors1);
            }
        }
    }
    public function verify($code)
    {
        $user = Users::where('confirmation_code', $code);

        if ($user->count() > 0) {
            $user->update([
                'confirmed' => 1,
                'confirmation_code' => null
            ]);
            $notification_status = 'Bạn đã xác nhận thành công';
        } else {
            $notification_status ='Mã xác nhận không chính xác';
        }
        return redirect(route('login'))->with('status', $notification_status);
    }
    public function authenticate(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'confirmed' => 1
        ];

        if (!Auth::attempt($credentials)) {
            return redirect()->back()
                ->withErrors([
                    'email'  =>  'Bạn không thể đăng nhập'
                ]);
        }

        return redirect('/');
    }
}
