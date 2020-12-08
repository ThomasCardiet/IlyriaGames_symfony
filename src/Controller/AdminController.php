<?php

namespace App\Controller;

use App\AppBundle\RankMethods;
use App\AppBundle\Rcon;
use App\AppBundle\ServerInformations;
use App\AppBundle\AdminMethods;
use App\AppBundle\ServerMethods;
use App\Entity\Main\FCategory;
use App\Entity\Main\FSubcategory;
use App\Entity\Main\FTopic;
use App\Entity\Main\news;
use App\Entity\Main\Notification;
use App\Entity\Main\Payment;
use App\Entity\Main\TCategory;
use App\Entity\Main\Ticket;
use App\Entity\Main\TMessage;
use App\Entity\Main\Users;
use App\Entity\Main\Visit;
use App\Entity\Server\beta;
use App\Entity\Server\players;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{

    private $_admin;
    private $_ranks;

    public function __construct()
    {
        $this->_admin = (new AdminMethods());
        $this->_ranks = (new RankMethods());
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, $page_name, $parameter)
    {

        $repo_users_site = $this->getDoctrine()->getRepository(Users::class);
        $repo_users_server = $this->getDoctrine()->getRepository(players::class);
        $repo_purchases = $this->getDoctrine()->getRepository(Payment::class);
        $repo_visits = $this->getDoctrine()->getRepository(Visit::class);
        $repo_topics = $this->getDoctrine()->getRepository(FTopic::class);

        if($this->getUser() === null || !$this->_admin->canAccessPanel($repo_users_server->findOneBy(['pseudo' => $this->getUser()->getUsername()])->getGroupe())) return $this->redirectToRoute('accueil');

        $player_server = $repo_users_server->findOneBy(['pseudo' => $this->getUser()->getUsername()]);

        $em_site = $this->getDoctrine()->getManager('default');
        $em_server = $this->getDoctrine()->getManager('server');

        $power = $this->_ranks->getRank($player_server->getGroupe())['power'];

        if($page_name == null) {
            foreach($this->_admin->getAdminPages() as $name => $values) {
                if($power >= $values['power']) {
                    $page_name = $name;
                    break;
                }
            }
        }

        if(($page_name !== "ajax" || $parameter === null) && (!isset($this->_admin->getAdminPages()[$page_name]) || $power < $this->_admin->getAdminPages()[$page_name]['power'])) return $this->redirectToRoute("admin");

        $values = array();
        $msg = array();

        switch ($page_name){

            // MEMBRES PAGE
            case 'membres':
                $player = $parameter;

                // SELECTED USER

                if($player != null) {

                    $user_site = $repo_users_site->findOneBy(array('pseudo' => $player));
                    $user_server = $repo_users_server->findOneBy(array('pseudo' => $player));

                    if(!empty($_POST)) {
                        switch ($_POST) {

                            case isset($_POST['changemail'], $_POST['mail']) :
                                $user_site->setEmail($_POST['mail']);
                                break;

                            case isset($_POST['changepass'], $_POST['pass']):
                                $options = [
                                    'cost' => 12,

                                ];
                                $hash_pass = password_hash($_POST['pass'], PASSWORD_BCRYPT, $options);
                                $user_site->setPassword($hash_pass);
                                break;

                            case isset($_POST['resetvote']):
                                $user_site->setVotes(0);
                                break;

                            case isset($_POST['changegrp'], $_POST['grade']):
                                $user_server->setGroupe($_POST['grade']);
                                break;

                            case isset($_POST['changedons'], $_POST['dons']):
                                $user_server->setPb($_POST['dons']);
                                break;

                            case isset($_POST['changeilycoins'], $_POST['ilycoins']):
                                $user_server->setPj($_POST['ilycoins']);
                                break;

                            case isset($_POST['delete']):
                                $em_site->remove($user_site);
                                $em_server->remove($user_server);

                                $em_site->flush();
                                $em_server->flush();

                                return $this->redirectToRoute('admin');
                        }

                        $em_site->persist($user_site);
                        $em_server->persist($user_server);

                        $em_site->flush();
                        $em_server->flush();
                    }

                    if($user_site == null || $user_server == null) return $this->redirectToRoute('admin');

                    return $this->render('admin/' . $page_name . '_player.html.twig', [
                        'user_site' => $user_site,
                        'user_server' => $user_server,
                        'ranks' => $this->_ranks->getRanksName(5),
                        'pages' => $this->_admin->getAdminPages(),
                        'power' => $power,
                        'page_name' => $page_name,
                    ]);

                }

                if(!empty($_POST)) {
                    switch ($_POST) {

                        // SEARCH_ACCOUNT
                        case isset($_POST['buttonSearch']) and !empty($_POST['textSearch']):

                            $value = htmlspecialchars($_POST['textSearch']);

                            $players = $repo_users_site->search($value);

                            $values['search_members'] = $players;

                            break;

                        // DELETE_ACCOUNT
                        case isset($_POST['delete']):

                            $user = $repo_users_site->findOneBy(array('id' => $_POST['id']));

                            $pseudo = $user->getPseudo();

                            $msg['msg_delete_success'] = '✔ ' . $pseudo . ' supprimé';

                            $em_site->remove($user);
                            $em_site->flush();

                            break;

                        // CHANGEPASS
                        case isset($_POST['changepass']):

                            if (!empty($_POST['pass'])) {

                                $options = [
                                    'cost' => 12,

                                ];

                                $hashpass = password_hash($_POST['pass'], PASSWORD_BCRYPT, $options);

                                $user = $repo_users_site->findOneBy(array('id' => $_POST['id']));

                                $user->setPassword($hashpass);

                                $em_site->persist($user);
                                $em_site->flush();

                                $msg['msg_password_success'] = '✔ Mot de passe changé';

                            } else $msg['msg_password_error'] = '✘ Merci de remplir le champ';

                            break;
                    }

                }

                $values['members'] = $repo_users_site->findAll();
                $values['players_5'] = $repo_users_site->findBy(array(), ['id' => 'DESC'], 5);
                $values['players_10'] = $repo_users_site->findBy(array(), ['id' => 'DESC'], 10);
                break;

            // STATS PAGE
            case 'stats':

                $today_date = new \DateTime();

                function getSubstractDayDate($day) {
                    return date('d/m/Y', mktime(0, 0, 0, date('m'), date('j') - $day, date('Y')));
                }

                // VISITS
                $visits = $repo_visits->findAll();

                $day_visits = array();

                foreach ($visits as $visit){
                    $string_date = date_format($visit->getCreateAt(), 'd/m/Y');

                    for($i = 0; $i < 4; $i++) {
                        if(!isset($day_visits[$i])) {
                            $day_visits[$i] = 0;
                        }

                        if($string_date === getSubstractDayDate($i)) $day_visits[$i]++;
                    }
                }

                if(empty($day_visits)) {
                    for($i = 0; $i < 4; $i++) {
                        $day_visits[$i] = 0;
                    }
                }

                $values['day_visits'] = $day_visits;
                $values['visits'] = $visits;

                // MEMBERS
                $members = $repo_users_site->findAll();

                $day_members = array();

                foreach ($members as $member){
                    $string_date = date_format($member->getDate(), 'd/m/Y');

                    for($i = 0; $i < 4; $i++) {
                        if(!isset($day_members[$i])) {
                            $day_members[$i] = 0;
                        }

                        if($string_date === getSubstractDayDate($i)) $day_members[$i]++;
                    }
                }

                if(empty($day_members)) {
                    for($i = 0; $i < 4; $i++) {
                        $day_members[$i] = 0;
                    }
                }

                $values['day_members'] = $day_members;
                $values['members'] = $members;

                // PURCHASES
                $purchases = $repo_purchases->findAll();

                $day_purchases = array();

                foreach ($purchases as $purchase){
                    $string_date = date_format($purchase->getCreateAt(), 'd/m/Y');

                    for($i = 0; $i < 4; $i++) {
                        if(!isset($day_purchases[$i])) {
                            $day_purchases[$i] = 0;
                        }

                        if($string_date === getSubstractDayDate($i)) $day_purchases[$i]++;
                    }
                }

                if(empty($day_purchases)) {
                    for($i = 0; $i < 4; $i++) {
                        $day_purchases[$i] = 0;
                    }
                }

                $values['day_purchases'] = $day_purchases;
                $values['purchases'] = $purchases;

                // TOPICS
                $topics = $repo_topics->findAll();

                $day_topics = array();

                foreach ($topics as $topic){
                    $string_date = date_format($topic->getCreateAt(), 'd/m/Y');

                    for($i = 0; $i < 4; $i++) {
                        if(!isset($day_topics[$i])) {
                            $day_topics[$i] = 0;
                        }

                        if($string_date === getSubstractDayDate($i)) $day_topics[$i]++;
                    }
                }

                if(empty($day_topics)) {
                    for($i = 0; $i < 4; $i++) {
                        $day_topics[$i] = 0;
                    }
                }

                $values['day_topics'] = $day_topics;
                $values['topics'] = $topics;
                break;

            // SUPPORT PAGE
            case 'support':

                $repo_ticket_category = $this->getDoctrine()->getRepository(TCategory::class);
                $repo_ticket = $this->getDoctrine()->getRepository(Ticket::class);

                // FORMS
                if(!empty($_POST)) {
                    switch ($_POST) {

                        //TICKET RESOLVED
                        case isset($_POST['ticket_resolved_submit']):
                            $ticket = $repo_ticket->find($parameter);
                            $ticket->setResolved(true);
                            $em_site->persist($ticket);
                            $em_site->flush();
                            break;

                        // SEND TICKET MESSAGE
                        case isset($_POST['ticket_message_submit'], $_POST['ticket_message_content']) && $this->getUser() !== null:

                            $message = new TMessage();
                            $message->setCreateAt(new \DateTime());
                            $message->setContent($_POST['ticket_message_content']);
                            $user = $repo_users_site->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
                            $user === null ? $this->redirectToRoute('security_login') : $user->addTicketsMessage($message);

                            $ticket = $repo_ticket->find($parameter);
                            $ticket->addTicketsMessage($message);

                            // NOTIF SECTION
                            $notif = new Notification();
                            $notif->construct($ticket->getOwner(), 1, [
                                'interlocutor' => $this->getUser()->getUsername(),
                                'ticket' => $ticket,
                            ], $request);
                            $em_site->persist($notif);


                            $em_site->persist($message);
                            $em_site->persist($user);
                            $em_site->persist($ticket);
                            $em_site->flush();
                            break;

                        // CREATE CATEGORY
                        case isset($_POST['create_category_submit']):

                            $create_category_name = htmlspecialchars($_POST['create_category_name']);

                            if (!empty($create_category_name)) {

                                if ($repo_ticket_category->findOneBy(array('name' => $create_category_name)) == null) {

                                    $category = new TCategory();
                                    $category->setName($create_category_name);

                                    $em_site->persist($category);
                                    $em_site->flush();

                                    $msg['msg_create_category_success'] = "Catégorie crée avec succès";

                                } else $msg['msg_create_category_error'] = "Ce nom est déjà utilisé";

                            } else $msg['msg_create_category_error'] = "Merci de remplir tous les champs";

                            break;

                        // DELETE CATEGORY
                        case isset($_POST['delete_category_submit']):

                            $delete_category_option = htmlspecialchars($_POST['delete_category_option']);

                            if (!empty($delete_category_option)) {

                                $category = $repo_ticket_category->find($delete_category_option);

                                $em_site->remove($category);

                                $em_site->flush();

                                $msg['msg_delete_category_success'] = "Catégorie supprimée avec succès";

                            } else $msg['msg_delete_category_error'] = "Merci de remplir tous les champs";

                            break;
                    }
                }

                $values['categories'] = $repo_ticket_category->findAll();
                $values['tickets'] = $repo_ticket->findAll();
                if($parameter != null) $values['ticket'] = $repo_ticket->find($parameter);
                break;

            // FORUM PAGE
            case 'forum':

                $repo_category = $this->getDoctrine()->getRepository(FCategory::class);
                $repo_subcategory = $this->getDoctrine()->getRepository(FSubcategory::class);

                // FORMS
                if(!empty($_POST)) {
                    switch ($_POST) {

                        // CREATE CATEGORY
                        case isset($_POST['create_category_submit']):

                            $create_category_name = htmlspecialchars($_POST['create_category_name']);

                            if(!empty($create_category_name)) {

                                if($repo_category->findOneBy(array('name' => $create_category_name)) == null) {

                                    $category = new FCategory();
                                    $category->setName($create_category_name);

                                    $em_site->persist($category);
                                    $em_site->flush();

                                    $msg['msg_create_category_success'] = "Catégorie crée avec succès";

                                } else $msg['msg_create_category_error'] = "Ce nom est déjà utilisé";

                            } else $msg['msg_create_category_error'] = "Merci de remplir tous les champs";

                            break;

                        // CREATE SUBCATEGORY
                        case isset($_POST['create_subcategory_submit']):

                            $create_subcategory_name = htmlspecialchars($_POST['create_subcategory_name']);
                            $create_subcategory_option = htmlspecialchars($_POST['create_subcategory_option']);

                            if(!empty($create_subcategory_name) && !empty($create_subcategory_option)) {

                                if($repo_subcategory->findOneBy(array('name' => $create_subcategory_name)) == null) {

                                    $sub_category = new FSubcategory();
                                    $sub_category->setName($create_subcategory_name);

                                    $category = $repo_category->find($create_subcategory_option);

                                    $category->addSubcategory($sub_category);

                                    $em_site->persist($sub_category);
                                    $em_site->persist($category);
                                    $em_site->flush();

                                    $msg['msg_create_subcategory_success'] = "Sous-Catégorie crée avec succès";

                                } else $msg['msg_create_subcategory_error'] = "Ce nom est déjà utilisé";

                            } else $msg['msg_create_subcategory_error'] = "Merci de remplir tous les champs";

                            break;

                        // DELETE CATEGORY
                        case isset($_POST['delete_category_submit']):

                            $delete_category_option = htmlspecialchars($_POST['delete_category_option']);

                            if(!empty($delete_category_option)) {

                                $category = $repo_category->find($delete_category_option);
                                $sub_categories = $category->getSubcategories();

                                foreach($sub_categories as $sub_category) {

                                    $em_site->remove($sub_category);

                                }

                                $em_site->remove($category);

                                $em_site->flush();

                                $msg['msg_delete_category_success'] = "Catégorie supprimée avec succès";

                            } else $msg['msg_delete_category_error'] = "Merci de remplir tous les champs";

                            break;

                        // DELETE SUBCATEGORY
                        case isset($_POST['delete_subcategory_submit']):

                            $delete_subcategory_option = htmlspecialchars($_POST['delete_subcategory_option']);

                            if(!empty($delete_subcategory_option)) {

                                $sub_category = $repo_subcategory->find($delete_subcategory_option);

                                $em_site->remove($sub_category);
                                $em_site->flush();

                                $msg['msg_delete_subcategory_success'] = "Sous-Catégorie supprimée avec succès";

                            } else $msg['msg_delete_subcategory_error'] = "Merci de remplir tous les champs";

                            break;

                    }
                }

                $values['categories'] = $repo_category->findAll();
                $values['sub_categories'] = $repo_subcategory->findAll();
                break;

            // NEWS PAGE
            case 'news':

                $repo_news = $this->getDoctrine()->getRepository(news::class);

                // NEWS
                if(!empty($_POST)) {
                    switch ($_POST) {

                        // CREATE NEWS
                        case isset($_POST['create_news_submit']):

                            $create_news_title = htmlspecialchars($_POST['create_news_title']);
                            $create_news_content = htmlspecialchars($_POST['create_news_content']);

                            if(!empty($create_news_title) && !empty($create_news_content)) {

                                $news = new news();
                                $news->setTitle($create_news_title)
                                    ->setContent($create_news_content)
                                    ->setCreateAt(new \DateTime());

                                $user_site = $repo_users_site->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
                                $user_site->addNews($news);

                                $em_site->persist($news);
                                $em_site->persist($user_site);
                                $em_site->flush();

                                $msg['msg_create_news_success'] = "News crée avec succès";

                            } else $msg['msg_create_news_error'] = "Merci de remplir tous les champs";

                            break;

                        // DELETE NEWS
                        case isset($_POST['delete_news_submit']):

                            $delete_news_option = htmlspecialchars($_POST['delete_news_option']);

                            if(!empty($delete_news_option)) {

                                $news = $repo_news->find($delete_news_option);
                                $comments = $news->getNewsComments();

                                foreach($comments as $comment) {

                                    $em_site->remove($comment);

                                }

                                $em_site->remove($news);

                                $em_site->flush();

                                $msg['msg_delete_news_success'] = "News supprimée avec succès";

                            } else $msg['msg_delete_news_error'] = "Merci de remplir tous les champs";

                            break;

                    }
                }

                $values['news'] = $repo_news->findAll();
                break;

            // BETA PAGE
            case 'bêta':

                $repo_beta = $this->getDoctrine()->getRepository(beta::class);

                if(!empty($_POST) && isset($_POST['beta_pseudo'])) {
                    $pseudo = $_POST['beta_pseudo'];
                    $beta = $repo_beta->findOneBy(['pseudo' => $pseudo]);
                    dump(isset($_POST['beta_submit_decline']));
                    if(isset($_POST['beta_submit_accept'])) {
                        $beta->setAccepted(true);
                        $em_server->persist($beta);
                    }
                    else if(isset($_POST['beta_submit_decline'])) $em_server->remove($beta);

                    $em_server->flush();
                }

                $values['betas'] = $repo_beta->findAll();
                break;

            // SANCTION PAGE
            case 'sanctions':

                $player_id = $parameter;

                if($player_id !== null){

                    $player = $repo_users_server->find($player_id);

                    $msg = [];
                    if(!empty($_POST)){

                        $command = null;

                        // KICK
                        if(isset($_POST["kick_submit"])){
                            $reason = $_POST["kick_reason"];
                            $command = "kick ".$player->getPseudo();
                            if(isset($reason) && !empty($reason)) $command.=" $reason";
                        }

                        // MUTE
                        if(isset($_POST["mute_submit"])){
                            $reason = $_POST["mute_reason"];
                            $type = $_POST["mute_type"];
                            if($type !== "Unlimited"){
                                $time = $_POST["mute_time"];
                                if(!empty($time)){
                                    $command = "mute temp ".$player->getPseudo();
                                    $command.=" $time $type";
                                    if(isset($reason) && !empty($reason)) $command.=" $reason";
                                }else $msg['error'] = "Merci de définir le temps";
                            }else {
                                $command = "mute def ".$player->getPseudo();
                                if(isset($reason) && !empty($reason)) $command.=" $reason";
                            }
                        }

                        // BAN
                        if(isset($_POST["ban_submit"])){
                            $reason = $_POST["ban_reason"];
                            $type = $_POST["ban_type"];
                            if($type !== "Unlimited"){
                                $time = $_POST["ban_time"];
                                if(!empty($time)){
                                    $command = "ban temp ".$player->getPseudo();
                                    $command.=" $time $type";
                                    if(isset($reason) && !empty($reason)) $command.=" $reason";
                                }else $msg['error'] = "Merci de définir le temps";
                            }else {
                                $command = "ban def ".$player->getPseudo();
                                if(isset($reason) && !empty($reason)) $command.=" $reason";
                            }
                        }

                        if($command !== null){
                            // RCON CONNEXION
                            $r = new Rcon();
                            $r->rcon("127.0.0.1",25547,"blast0110*");
                            if($r->Auth())
                            {
                                $r->rconCommand($command);
                                $msg['success'] = "Commande exécutée avec succès!";
                            }else {
                                $msg['error'] = "L'authentification RCON a rencontré un problème!";
                            }
                        }
                    }

                    return $this->render('admin/'.$page_name.'_player.html.twig', [
                        'player' => $player,
                        'msg' => $msg,
                        'pages' => $this->_admin->getAdminPages(),
                        'power' => $power,
                        'page_name' => $page_name,
                    ]);
                }
                break;

            case 'console':

                $values['servers'] = (new ServerMethods())->getServers(true);
                break;

            // AJAX PAGE
            case 'ajax':

                $json = null;
                $path = (new ServerInformations(null))->getPath();
                switch ($parameter) {

                    case "getConsole":
                        $server = $_POST["server"];
                        $log = (new ServerInformations($server))->getLogs();
                        $json = json_encode((new ServerMethods())->convert_to_utf8_recursively($log));
                        break;

                    case "sendServerCmd":
                        $server = $_POST['server'];
                        $r = (new Rcon())->RconByServerName($server);
                        if(!$r->isConnected()) {
                            $json = json_encode($r->error);
                            break;
                        }
                        if($r->Auth()) {
                            $r->rconCommand($_POST['cmd']);
                            $json = json_encode([
                                'state' => true,
                                'msg' => 'Commande envoyée avec succès'
                            ]);
                            break;
                        }
                        $json = json_encode([
                            'state' => false,
                            'msg' => 'Une erreur est survenue avec la connexion RCON'
                        ]);
                        break;

                    case "getOnlinePlayers":

                        $json = json_encode((new ServerMethods())->getOnlinePlayers($em_server));
                        break;

                    case "getCurrentSanctions":
                        $player_id = $_POST['player_id'];
                        $player = $repo_users_server->find((int)$player_id);
                        function getQuery($table) {
                            return "SELECT * FROM $table where pseudo = ?";
                        }
                        $tables = ["bannis", "mutes"];
                        $sanctions = [];
                        foreach($tables as $table){
                            $statement = $em_server->getConnection()->prepare(getQuery($table));
                            $statement->execute([$player->getPseudo()]);
                            $result = $statement->fetch();
                            if($result !== false) {
                                $result['type'] = $table;
                                $sanctions[$table] = $result;
                            }
                        }

                        $json = json_encode($sanctions);
                        break;

                    case "removeSanction":
                        $id = $_POST['id'];
                        $table = $_POST['type'];
                        $statement = $em_server->getConnection()->prepare("DELETE FROM $table WHERE id = ?");
                        $statement->execute([$id]);
                        $json = json_encode("Sanction supprimée avec succès!");
                        break;

                    case "addSanction":

                        $command = null;
                        $type = $_POST['type'];
                        $player_id = $_POST['player_id'];
                        $player = $repo_users_server->find((int)$player_id);
                        $sanction = $_POST['sanction'];

                        switch ($type){

                            case 'kick':
                                $command = "kick ".$player->getPseudo();
                                if(isset($sanction["reason"])) $command.=" ".$sanction["reason"];
                                break;

                            case 'ban':
                                $type = $sanction["type"];
                                if($type !== "Unlimited"){
                                    $time = $sanction["time"];
                                    if(!empty($time)){
                                        $command = "ban temp ".$player->getPseudo();
                                        $command.=" $time $type";
                                        if(isset($sanction["reason"])) $command.=" ".$sanction["reason"];
                                    }else $json = json_encode("Merci de définir le temps");
                                }else {
                                    $command = "ban def ".$player->getPseudo();
                                    if(isset($sanction["reason"])) $command.=" ".$sanction["reason"];
                                }
                                break;

                            case 'mute':

                                $type = $sanction["type"];
                                if($type !== "Unlimited"){
                                    $time = $sanction["time"];
                                    if(!empty($time)){
                                        $command = "mute temp ".$player->getPseudo();
                                        $command.=" $time $type";
                                        if(isset($sanction["reason"])) $command.=" ".$sanction["reason"];
                                    }else $json = json_encode("Merci de définir le temps");
                                }else {
                                    $command = "mute def ".$player->getPseudo();
                                    if(isset($sanction["reason"])) $command.=" ".$sanction["reason"];
                                }
                                break;
                        }

                        if($command !== null){
                            $first_online_server = (new ServerMethods())->getServers(true, true)[0];
                            $rcon = (new Rcon())->RconByServerName($first_online_server);
                            if($rcon->Auth()){
                                $rcon->rconCommand($command);
                            }
                        }
                        break;
                }

                if ($json !== null) {
                    echo $json;
                    return $this->render('admin/ajax.html.twig');
                }
                break;

        }

        return $this->render('admin/' . $page_name . '.html.twig', [
            'values' => $values,
            'msg' => $msg,
            'page_name' => $page_name,
            'power' => $power,
            'pages' => $this->_admin->getAdminPages(),
        ]);
    }
}
