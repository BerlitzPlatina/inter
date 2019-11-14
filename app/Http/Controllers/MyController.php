<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Users;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;

class MyController extends Controller
{
    public function ShowB(){
        return view('buttons');
    }

    public function ShowC(){
        return view('cards');
    }

    public function RedirectB(){
        return redirect('buttons');
    }

    public function getIndex() {
        return view('index');
    }

    public function getRegister(){
        return view('register');
    }
    public  function postRegister(Request $request){
        $rules=[
            'firstname'=>'required|max:100|alpha_num',
            'emailID'=>'required|email',
            'passwordinput'=>'required|min:8|confirmed'
        ];
        $message=[
            'firstname.required'=>'Không được bỏ trống',
            'firstname.max'=>'Ký tự không được quá 100',
            'firstname.firstname'=>'Không được nhập ký tự đặc biệt',
            'emailID.required'=>'Email Là trường bắt buộc',
            'emailID.email'=>'Email không đúng định dạng',
            'passwordinput.required'=>'Mật khẩu không được bỏ trống',
            'passwordinput.min'=>'Mật khẩu phải ít nhất 8 kí tự',
            'passwordinput.confirmed'=>'Mật khẩu phải trùng nhau'
        ];
        $validator = Validator::make ($request->all(),$rules,$message);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        else {
            $confirmation_code = time() . uniqid(true);

            $users = new Users;
            $users->email = $request->emailID;
            $users->name = $request->firstname;
            $users->password = Hash::make($request->passwordinput);
            $users->confirmation_code = $confirmation_code;
            $users->confirmed = 0;
            //$users->name = $request->$nameID;
            $to = $users->email;
            Mail::send('confirm', array('code' => $confirmation_code, ''), function ($message) use ($to) {
                $message->to($to, 'visstor')
                    ->subject('Verify your email address');
            });
            $users->save();
            //return redirect()->action('');
            return redirect(route('login'))->with('status', 'Vui lòng xác nhận tài khoản email');
        }
    }
    public function getForgot(){
        return view('forgot');
    }
    public function postForgot(Request $request){
        $test_1 = Str::random(8);
        $test = new Users;
        $test = Users::where('email',$request->emailID)->value('id');
        //$flight = new Flight;
        $test = Users::find($test);
        $to=$request->emailID;
        $test->password= Hash::make($test_1);
        Mail::send('confirm_password', array('code'=>$test_1,''), function($message) use ($to) {
            $message->to($to,'visstor')
                ->subject('Verify your email address');
        });
        $test->save();
    }
}
