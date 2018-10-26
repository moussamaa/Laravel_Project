<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Auth;
use App\User;
use Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;



    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/task';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver)
    {
        if ($driver ==  'github')
            return Socialite::driver('gitHub')
            ->scopes(['read:user', 'public_repo'])
            ->redirect();
            
        elseif ($driver ==  'twitter') 
             return Socialite::driver('twitter')
            ->redirect();
        elseif ($driver ==  'facebook') 
             return Socialite::driver('facebook')
             ->redirect();
      
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {

        $user = Socialite::driver($provider)->user();
         $authUser = $this->findOrCreateUser($user, $provider);
        Auth::login($authUser, true);
        return redirect($this->redirectTo);

    }
    public function findOrCreateUser($user, $provider)
    {
        $authUser = User::where('provider_id', $user->id)->first();
        $authUserWithEmail = User::where('email', $user->email)->first();
        if ($authUser ) {
            return $authUser;
        }
        if ( $authUserWithEmail) {
            return $authUserWithEmail;
        }

        return User::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'provider' => $provider,
            'image' => $user->avatar,
            'provider_id' => $user->id, 

        ]);


}

public function logout(){
    Auth::logout(); 
    return redirect("/");
}

}
