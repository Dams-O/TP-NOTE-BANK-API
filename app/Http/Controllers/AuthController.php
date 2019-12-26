<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAccount;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Enregistrer un nouveau user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'birthday' => 'required|string',
            'address' => 'required|string',
            'civility' => 'required|string',
            'money' => 'required|integer',
            'password' => 'required',
        ]);

        try {

            $user = new UserAccount;
            $numberRandom = random_int(0000000, 9999999);
            $user->id_account = 'MYBAN' . $numberRandom . 'B';
            $user->firstName = $request->input('firstName');
            $user->lastName = $request->input('lastName');
            $user->birthday = $request->input('birthday');
            $user->address = $request->input('address');
            $user->civility = $request->input('civility');
            $user->money = $request->input('money');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed! '. $e], 409);
        }

    }

    /**
     * Récupérer un token JWT via les informations utilisateurs.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'id_account' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['id_account', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Ferme une session et invalide le token JWT.
     *
     * @param  Request  $request
     * @return Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Utilisateur déconnecté'], 200);
    }

}