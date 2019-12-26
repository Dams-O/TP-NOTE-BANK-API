<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


use Tymon\JWTAuth\Contracts\JWTSubject;

class UserAccount extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * Spécification de la clé primaire
     *
     * @var unique
     */
    protected $primaryKey = 'id';
    
    /**
     * Les attributs du compte utilisateur
     *
     * @var array
     */
    protected $fillable = [
        'id_account', 'firstName', 'lastName', 'birthday', 'address', 'civility', 'money'
    ];

    /**
     * Les attributs sensibles du compte
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];




    /**
     * Permet de récupérer la clé JWT de l'utilisateur connecté
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Permet de customiser le payload du token JWT
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
