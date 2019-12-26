<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\UserAccount;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use DB;

class BankOpsController extends Controller
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
     * Retirer ou ajouter de l'argent au compte connecté
     *
     * @return Response
     */
    public function addOrWithdrawMoney(Request $request)
    {
        //Le montant à ajouter ou retirer est en paramètre de la requete
        //Pour retirer il suiffit que le montant soit négatif
        $montant = $request->montant;
        //On récupère l'information sur l'argent disponible sur le compte connecté
        $money = DB::table('user_accounts')
            ->where('id', Auth::user()['id'])
            ->pluck('money')->toArray();
        //On calcul l'état du compte après la transaction
        $newMoney = $money[0] + $montant;
        //On vérifie que le compte ne soit pas négatif après la transaction
        if($newMoney > 0){
            DB::table('user_accounts')
                ->where('id', Auth::user()['id'])
                ->update(['money' => $newMoney]);
            return response()->json(['message' => 'Transaction reussie'], 200);
        }
        else{
            return response()->json(['message' => 'Retrait impossible, compte insuffisant'], 200);
        }
    }

    /**
     * Effectuer un virement vers un compte de notre banque
     *
     * @return Response
     */
    public function intraTransfert(Request $request)
    {
        //Le montant à envoyer au compte destinataire est en paramètre de la requete
        //Pas de valeur négative ici
        $montant = $request->montant;
        //On récupère l'information sur l'argent disponible sur le compte connecté
        $money = DB::table('user_accounts')
            ->where('id', Auth::user()['id'])
            ->pluck('money')->toArray();
        //Le compte destinataire
        $compteDest = $request->dest;
        //On calcul l'état du compte après la transaction
        $newMoney = $money[0] - $montant;
        //On vérifie que le compte ne soit pas négatif après la transaction
        if($newMoney > 0){
            //On retire le montant du virement au compte débitaire
            DB::table('user_accounts')
                ->where('id', Auth::user()['id'])
                ->update(['money' => Auth::user()['money'] - $montant]);
            //On recupère l'information du compte destinataire
            $moneyAccountDest = DB::table('user_accounts')
                ->where('id_account', $compteDest)
                ->pluck('money')->toArray();
            //On ajoute le montant du virement au compte destinataire
            DB::table('user_accounts')
                ->where('id_account', $compteDest)
                ->update(['money' => $moneyAccountDest[0] + $montant]);
            return response()->json(['message' => 'Transaction vers '.$compteDest.' effectue avec succes'], 200);
        }
        else{
            return response()->json(['message' => 'Virement impossible, compte insuffisant'], 200);
        }
    }

    /**
     * Effectuer un virement entre deux comptes de banques différentes.
     *
     * @return Response
     */
    public function externalTransfert(Request $request)
    {
        //Le montant à envoyer au compte destinataire est en paramètre de la requete
        //Pas de valeur négative ici
        $montant = $request->montant;
        //On récupère l'information sur l'argent disponible sur le compte connecté
        $money = DB::table('user_accounts')
            ->where('id', Auth::user()['id'])
            ->pluck('money')->toArray();
        //Le compte destinataire
        $compteDest = $request->dest;
        //On calcul l'état du compte après la transaction
        $newMoney = $money[0] - $montant;
        //On vérifie que le compte ne soit pas négatif après la transaction
        if($newMoney > 0){
            //Ici on reforme le token que l'on va utilisé, avec non pas Bearer mais Bankid
            //Qui sera attendu du côté de la banque cible
            $apiman = "Bankid {$this->accesstokenApi()}";
            $client = new Client();

            //On retrouve ici l'adresse utilisé pour communiquer aux autres banques et faire
            //une demande de transaction
            $response = $client->post('http://autre-banque.com/externalTransfert', [
                'headers' => 
                [
                    'authorization' => $apiman,
                    'content-type' => 'application/json',
                ],
                //Les informations relatives à la transaction
                'json' =>
                [
                    'débiteur' => $comptedeb,
                    'comptecible' => $comptecible,
                    'montant' => $montant
                ],
            ]);
            //Cette partie correspond à la phase de retour, ce qu'aura répondu le 
            //serveur de banque distant, on attend d'ailleurs une réponse avec une
            //authorization de type Bankid
            $data = json_decode((string) $response->getBody(), true);
            if ($data['token_type'] == "Bankid") {
                session()->put('token', $data);
                return redirect('/');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Response',
                ], 401);
            }
            //On retourne ici un mesage indiquant que la transaction c'est déroulé avec succes
            return response()->json(['message' => 'Transaction effectué avec success', 200]);
        }
        else{
            return response()->json(['message' => 'Virement impossible, compte insuffisant'], 200);
        }
    }

}