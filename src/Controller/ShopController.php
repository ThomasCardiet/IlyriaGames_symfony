<?php

namespace App\Controller;

use App\Entity\Main\Payment;
use App\Entity\Main\Users;
use App\Entity\Main\news;
use App\Entity\Server\players;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{

    // REAL

    /**
     * @Route("/boutique/validation", name="shop_validation")
     */
    public function shop_validation($mode_value, $success_value, $id_value, SessionInterface $session) {

        $page = 4;

        // PAYPAL

        if($mode_value === 'paypal') {
            $raw_post_data = file_get_contents('php://input');
            $raw_post_array = explode('&', $raw_post_data);
            $PostData = array();

            foreach ($raw_post_array as $keyval) {
                $keyval = explode ('=', $keyval);
                if (count($keyval) == 2)
                    $PostData[$keyval[0]] = urldecode($keyval[1]);
            }

            $req = 'cmd=_notify-validate';
            $get_magic_quotes_exists = false;
            if (function_exists('get_magic_quotes_gpc')) {
                $get_magic_quotes_exists = true;
            }
            foreach ($PostData as $key => $value) {
                if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                    $value = urlencode(stripslashes($value));
                } else {
                    $value = urlencode($value);
                }
                $req .= "&".$key."=".$value;
            }


            $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

            if ( !($res = curl_exec($ch)) ) {
                curl_close($ch);
                exit;
            }
            if (strcmp ($res, "VERIFIED") == 0) {

                if ($_POST['payment_status'] === "Completed"
                    AND $_POST['receiver_email'] === 'ipvpgun_ytb@yahoo.fr') {

                    $repo = $this->getDoctrine()->getRepository(players::class);

                    $userServer = $repo->findOneBy(array('pseudo' => $_POST['custom']));

                    $pb = $userServer->getPb();

                    $offer_price = (double)$_POST['mc_gross'];
                    $offer_dons = ($offer_price + 0.01) * 10;
                    $offer_bonus = floor($offer_dons * (0.08 + ((floor($offer_dons / 100) / 20) - (floor($offer_dons / 1000)) / 5.56)));

                    $pb_count = $offer_dons + $offer_bonus;

                    $userServer->setPb($pb + $pb_count);

                    $em_server = $this->getDoctrine()->getManager('server');
                    $em_server->persist($userServer);
                    $em_server->flush();

                    $payment = new Payment();
                    $payment->setOwner($userServer->getPseudo())
                        ->setPrice($offer_price)
                        ->setMode($mode_value)
                        ->setCreateAt(new \DateTime())
                        ->setPb($pb_count);

                    $em_site = $this->getDoctrine()->getManager('default');
                    $em_site->persist($payment);
                    $em_site->flush();
                }
            }

            curl_close($ch);
        }

        // DEDIPASS

        if($mode_value === 'dedipass') {
            $code = isset($_POST['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['code']) : '';
            if( empty($code) ) {
                return $this->redirectToRoute('shop_validation', array('mode_value' => 0, 'success_value' => 0));
            }

            $dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?public_key=61a9ab0ed815ce262b7b6e6f5d7fe7a4&private_key=348ce7957a0936a23bc760803ccc4d63ad6f0411&code=' . $code);
            $dedipass = json_decode($dedipass);
            if($dedipass->status == 'success') {

                $repo = $this->getDoctrine()->getRepository(players::class);

                $userServer = $repo->findOneBy(array('pseudo' => $_POST['custom']));

                $pb = $userServer->getPb();

                $userServer->setPb($pb + $dedipass->virtual_currency);

                $em_server = $this->getDoctrine()->getManager('server');
                $em_server->persist($userServer);
                $em_server->flush();

                $payment = new Payment();
                $payment->setOwner($_POST['custom'])
                    ->setPrice($dedipass->payout)
                    ->setMode($mode_value)
                    ->setCreateAt(new \DateTime())
                    ->setPb($dedipass->virtual_currency);

                $em_site = $this->getDoctrine()->getManager('default');
                $em_site->persist($payment);
                $em_site->flush();

                return $this->redirectToRoute('shop_validation', array('mode_value' => 0, 'success_value' => true, "id_value" => $payment->getId()));

            }

            // Le code est invalide
            return $this->redirectToRoute('shop_validation', array('mode_value' => 0, 'success_value' => 0));
        }

        if($id_value !== null) {
            $repo = $this->getDoctrine()->getRepository(Payment::class);
            $payment = $repo->find($id_value);

            if($payment !== null) {

                return $this->render('accueil/shop/step_4.html.twig', [
                    'success_value' => $success_value,
                    'payment' => $payment,
                    'page' => $page,
                ]);
            }else return $this->redirectToRoute('shop');
        }

        return $this->render('accueil/shop/step_4.html.twig', [
            'success_value' => $success_value,
            'page' => $page,
        ]);

    }

    /**
     * @Route("/boutique", name="shop")
     */
    public function shop($playerid_value, $mode_value, $offer_value, SessionInterface $session) {

        if($offer_value !== null) $offer_value = (int)$offer_value;

        $page = 0;

        if($offer_value !== null || $mode_value === "dedipass") $page = 3;
        else if($mode_value !== null) $page = 2;
        else if($playerid_value !== null) $page = 1;

        $msg_error = [];
        $repo_user_site = $this->getDoctrine()->getRepository(Users::class);

        if(!empty($_POST)) {
            switch ($page) {

                // PAGE DESTINATAIRE
                case 0:

                    $player = null;

                    if(isset($_POST['self_choose'])) $player = $repo_user_site->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
                    else if(isset($_POST['other_choose'], $_POST['other_choose_value'])) {

                        $pseudo_value = htmlspecialchars($_POST['other_choose_value']);

                        if($repo_user_site->findOneBy(array('pseudo' => $pseudo_value)) !== null) {

                            $player = $repo_user_site->findOneBy(array('pseudo' => $pseudo_value));

                        }else $msg_error[] = 'Aucun compte inscrit sur le serveur ne possède ce pseudo.';

                    }else $msg_error[] = 'Une erreur est survenue merci de réessayer.';

                    if($player != null) return $this->redirectToRoute('shop', array('playerid_value' => $player->getId()));

                    break;

                // PAGE OFFERS
                case 2:

                    if(!empty($_POST['offer_dons'])) {

                        if($repo_user_site->find($playerid_value) === null) $msg_error[] = "L'url a été modifié, merci de réessayer";
                        else return $this->redirectToRoute('shop', array('playerid_value' => $playerid_value,
                            'mode_value' => $mode_value,
                            'offer_value' => $_POST['offer_dons']));

                    } else $msg_error[] = "Aucune offre n'a été choisie.";

                    break;

            }
        }

        $user = null;
        if($playerid_value !== null) $user = $repo_user_site->find($playerid_value);

        $offer = ['user' => $user,
            'mode' => $mode_value,
            'dons' => $offer_value,
            'bonus' => floor($offer_value * (0.08 + ((floor($offer_value/100)/20) - (floor($offer_value/1000))/5.56))),
            'price' => ($offer_value/10) - 0.01];

        $session->set('facture', $offer);

        $payment_utils = [];

        // PAYPAL
        if($mode_value === "paypal") {

            $payment_utils = [];

            $email_paypal = "ipvpgun_ytb@yahoo.fr";/*email associé au compte paypal du vendeur*/
            $item_nom = $offer['dons'] . "(+" . $offer['bonus'] . ") Point(s) Boutique"; /*Nom du produit*/
            $url_retour = 'http://' . $_SERVER['HTTP_HOST'] . '/boutique/validation/0-1';/*page de remerciement à créer*/
            $url_cancel = 'http://' . $_SERVER['HTTP_HOST'] . '/boutique/validation/0-0'; /* page d'annulation d'achat SI RETOUR */

            $url_confirmation = 'http://' . $_SERVER['HTTP_HOST'] . '/boutique/validation/paypal';
            /* fin déclaration des variables */

            $link = 'https://www.paypal.com/cgi-bin/webscr';
            $postfields = array(
                'cmd' => '_xclick',
                'cn' => $item_nom,
                'business' => $email_paypal,
                'item_name' => $item_nom,
                'item_number' => '1',
                'quantity' => '1',
                'amount' => $offer['price'],
                'currency_code' => 'EUR',
                'no_note' => '1',
                'no-shipping' => '1',
                'tax' => '0.00',
                'bn' => 'PP-BuyNowBF',
                'lc' => 'FR',
                'notify_url' => $url_confirmation,
                'cancel_return' => $url_cancel,
                'return' => $url_retour,
                'custom' => $offer['user']->getPseudo()
            );

            $payment_utils = [
                "link" => $link,
                "postfields" => $postfields,
            ];
        }

        if($offer_value !== null && $offer_value < 10) $msg_error[] = "L'url a été modifiée, merci de réessayer";

        return $this->render('accueil/shop/step_' . $page . '.html.twig', [
            'page' => $page,
            'playerid_value' => $playerid_value,
            'mode_value' => $mode_value,
            'offer_value' => $offer_value,
            'msg_error' => $msg_error,
            'offer' => $offer,
            'payment_utils' => $payment_utils,
        ]);

    }

}
