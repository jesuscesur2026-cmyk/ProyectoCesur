<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/email/verify';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:20'],
            'ape1' => ['required', 'string', 'max:20'],
            'ape2' => ['string', 'max:35', 'nullable'],
            'fechaNac' => ['required', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:usuario'],
            'password' => ['required', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/', 'min:8'],
            'password-confirm' => ['required', 'min:8', 'same:password'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Usuario
     */
    protected function create(array $data)
    {
        // Ensure a default role exists; create or fetch the "usuario" role and use its id
        $rolModel = Rol::firstOrCreate(['rol' => 'usuario']);
        $idRol = $rolModel->idRol;

        return Usuario::create([
            'nombre' => $data['nombre'],
            'apellido1' => $data['ape1'],
            'apellido2' => $data['ape2'],
            'fecNacimiento' => $data['fechaNac'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'idRol' => $idRol,
        ]);
    }

    /**
     * Handle a registration request for the application without auto-login.
     * This creates the user and sends the verification email but does not
     * authenticate the user until they verify their email.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));

        // Do not log the user in automatically. Redirect to login with notice.
        return redirect()->route('login')->with('status', __('A verification link has been sent to your email. Please verify before logging in.'));
    }
}
