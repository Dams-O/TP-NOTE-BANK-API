<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\UserAccount;

class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Ici l'on appel à la base de se controller le middleware auth
        // dont le rôle est de vérifié l'authentification, il est actif
        // pour chaque routes
        $this->middleware('auth');
    }

    /**
     * Retourne le profil de l'utilisateur connecté
     *
     * @return Response
     */
    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

}