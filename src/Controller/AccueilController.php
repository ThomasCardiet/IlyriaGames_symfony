<?php

namespace App\Controller;

use App\AppBundle\AdminMethods;
use App\Entity\Main\NComments;
use App\Entity\Main\news;
use App\Entity\Main\TCategory;
use App\Entity\Main\Ticket;
use App\Entity\Main\TMessage;
use App\Entity\Main\Users;
use App\Entity\Main\Visit;
use App\Entity\Server\beta;
use App\Entity\Server\players;
use App\Form\BetaFormType;
use App\Repository\Main\VisitRepository;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{

    // VUES INCREMENTATION
    public function __construct(VisitRepository $repo_visits, ManagerRegistry $managerRegistry)
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $em = $managerRegistry->getManager();

        $exist_visit = $repo_visits->findOneBy(array('ip_adresse' => $ip_address));

        if($exist_visit == null) {
            $visit = new Visit();
            $visit->setIpAdresse($ip_address);
            $visit->setCreateAt(new \DateTime());

            $em->persist($visit);
            $em->flush();
        }
    }

    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {
        $nbPlayers = null; $nbMaxPlayer = null;

        $statut = @fsockopen("localhost", 25565, $errno, $errstr, 0);
        if($statut) {
            fwrite($statut, "\xFE\x01");

            $d = fread($statut, 1024);

            if ($d != false AND substr($d, 0, 1) == "\xFF") {
                $d = substr($d, 3);
                $d = mb_convert_encoding($d, 'UTF-8', 'UCS-2');
                $d = explode("\x00", $d);
                fclose($statut);
                $nbPlayers = $d[4];
                $nbMaxPlayer = $d[5];
            }
        }

        $repo_news = $this->getDoctrine()->getRepository(news::class);
        $users_site_repo = $this->getDoctrine()->getRepository(Users::class);
        $em_site = $this->getDoctrine()->getManager('default');

        if(!empty($_POST)) {

            switch ($_POST) {

                case isset($_POST['comment_submit'], $_POST['comment_content']):

                    $news = $repo_news->find((int)$_POST['comment_news_id']);
                    $content = htmlspecialchars($_POST['comment_content']);

                    $comment = new NComments();
                    $comment->setCreateAt(new \DateTime())
                        ->setContent($content);

                    $news->addNewsComment($comment);
                    $user = $users_site_repo->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
                    $user->addNewsComment($comment);

                    $em_site->persist($news);
                    $em_site->persist($comment);
                    $em_site->persist($user);
                    $em_site->flush();
                    break;

            }

        }

        return $this->render('accueil/index.html.twig', [
            'nbPlayers' => $nbPlayers,
            'nbMaxPlayers' => $nbMaxPlayer,
            'news' => $repo_news->findAll()
        ]);
    }

    // ACCOUNT SECTION
    public function email_confirm($email_value, $id_value, $key_value, Request $request, \Swift_Mailer $mailer)
    {
        $repo_users = $this->getDoctrine()->getRepository(Users::class);
        $em = $this->getDoctrine()->getManager('default');

        $user = null;

        if(empty($email_value) || $id_value === null || empty($key_value)) return $this->redirectToRoute('account');

        $msg = [
            'error' => [],
            'success' => []
        ];

        // EMAIL VERIF
        if(!strpos($email_value, "@") || !strpos($email_value, "."))
            $msg['error'][] = 'Format email incorrect';

        if(!empty($repo_users->findOneBy(['email' => $email_value])))
            $msg['error'][] = 'Email déjà utilisé';

        // USER VERIF
        if(empty($repo_users->find($id_value)))
            $msg['error'][] = 'Utilisateur introuvable';
        else {
            $user = $repo_users->find($id_value);
            $confirm_key = $user->getConfirmKey();
            if(empty($confirm_key) || $confirm_key !== $key_value)
                $msg['error'][] = 'Clée de confirmation incorrect';

            $email = $user->getEmail();
            if($email !== "NONE")
                $msg['error'][] = 'Email déjà confirmé';
        }

        // KEY VERIF
        if(empty($repo_users->findOneBy(['confirmkey' => $key_value])))
            $msg['error'][] = 'Clée de confirmation introuvable';

        $email_confirmation = [
            'error' => $msg['error'],
        ];

        // RETURN ERROR
        if(!empty($msg['error'])) return $this->account(null, $email_confirmation, $request, $mailer);

        $user->setEmail($email_value);
        $em->persist($user);
        $em->flush();
        $msg['success'][] = "Email $email_value confirmé";

        $email_confirmation = [
            'success' => $msg['success'],
        ];

        return $this->account(null, $email_confirmation, $request, $mailer);

    }

    public function account($playerid_value, $email_confirmation = null, Request $request, \Swift_Mailer $mailer)
    {

        $repo_site = $this->getDoctrine()->getRepository(Users::class);
        $repo_server = $this->getDoctrine()->getRepository(players::class);
        $em = $this->getDoctrine()->getManager('default');

        $user = $this->getUser();

        // FORM
        if(!empty($_POST)){

            // CONFIRM EMAIL
            if(isset($_POST['cmail_submit'])){
                if(!empty($_POST['cmail'])){
                    $email = $_POST['cmail'];
                    if(!$repo_site->findOneBy(['email' => $email])){
                        $key = $repo_site->generateRandomKey(16);
                        $this->getUser()->setConfirmKey($key);
                        $em->persist($this->getUser());
                        $em->flush();
                        $link = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath()."/compte/emailconfirm/".$email."/".$this->getUser()->getID()."/".$key;
                        $message = (new \Swift_Message('Email de Confirmation'))
                            ->setFrom('ilyriaGames@gmail.com')
                            ->setTo($email)
                            ->setBody(
                                $this->renderView('samples/sample.email.html.twig', [
                                        'link' => $link,
                                    ]
                                ),
                                'text/html'
                            );

                        $mailer->send($message);
                        $email_confirmation['success'][] = "Un email de confirmation a été envoyé à $email";

                    }else $email_confirmation['error'][] = "Cet email est déjà utilisé";
                }else $email_confirmation['error'][] = "Le champ email est vide";
            }
        }

        if($repo_site->find((int)$playerid_value) !== null) {
            $user = $repo_site->find((int)$playerid_value);
        }

        if(is_null($user)) {
            return $this->redirectToRoute('accueil');
        }
        $userServer = $repo_server->findOneBy(array('pseudo' => $user->getUsername()));

        $friends = explode(',', $userServer->getAmis());

        if(strlen($userServer->getAmis()) == 0) $friends = array();

        // NOTIFICATIONS
        $notifications = [
            'true' => [],
            'false' => [],
        ];
        foreach ($user->getNotifications() as $notif){
            if($notif->getReaded() === true) $notifications['true'][] = $notif;
            else $notifications['false'][] = $notif;
        }

        // GAME STATS
        $game_stats = [
            'donjon' => [],
            'goulag' => [],
            'rush' => [],
        ];

        $em_server = $this->getDoctrine()->getManager('server');
        foreach($game_stats as $key => $stat){
            try {
                $RAW_QUERY = 'SELECT * FROM '.$key.'_stats where pseudo = :pseudo;';
                $statement = $em_server->getConnection()->prepare($RAW_QUERY);
                $statement->bindvalue('pseudo', $user->getPseudo());
                $statement->execute();
                $results = $statement->fetchAll();

                foreach($results as $result) {
                    unset($result['id'], $result['pseudo']);
                    $game_stats[$key] = $result;
                }
            }catch (TableNotFoundException $e){}
        }

        return $this->render('accueil/account/account.html.twig', [
            'user' => $user,
            'userServer' => $userServer,
            'canAccessPanel' => (new AdminMethods())->canAccessPanel($userServer->getGroupe()),
            'rankColor' => $userServer->getRankColor(),
            'friends' => $friends,
            'email_confirmation' => $email_confirmation,
            'notifications' => $notifications,
            'game_stats' => $game_stats,
        ]);
    }

    /**
     * @Route("/comptes", name="accounts")
     */
    public function accounts()
    {

        $error_msg = [];
        $repo_user_site = $this->getDoctrine()->getRepository(Users::class);
        $repo_user_server = $this->getDoctrine()->getRepository(players::class);
        $users_list = [];

        if(isset($_POST['search_submit'])) {

            if(!empty($_POST['search_value'])) {

                $users_list = $repo_user_site->search($_POST['search_value']);

            }else $error_msg[] = "Aucune valeur n'a été entrée";

        }

        return $this->render('accueil/account/account_search.html.twig', [
            'error_msg' => $error_msg,
            'users_list' => $users_list,
            'repo_user_server' => $repo_user_server,
        ]);
    }

    /**
     * @Route("/voter", name="voter")
     */
    public function voter(Request $request, ManagerRegistry $managerRegistry, SessionInterface $session, $id_site) {

        $user = $this->getUser();

        $error_msg = [];
        $success_msg = [];

        // REWARD SET INFORMATIONS
        $reward_info = function () {

            if($this->getUser() === null) return null;

            $unit_step = 20;
            $unit_step_reward = 1;
            $actual = $this->getUser()->getVotes();
            $next_step = $unit_step * (floor($actual/$unit_step)) + $unit_step;
            $steps_collected = (floor($actual/$unit_step));
            $less_next_step = $next_step - $actual;
            $actual_next_step = $unit_step - $less_next_step;
            $percent_next_step = ($actual_next_step/$unit_step) * 100;
            $reward_already_collected = $steps_collected * $unit_step_reward;
            $is_new_step = ($actual === ($unit_step * (int)(floor(($actual-1)/$unit_step)) + $unit_step)-1);

            return [
                'ilyCoins_per_vote' => 8,
                'unit_step' => $unit_step,
                'unit_step_reward' => $unit_step_reward,
                'actual' => $actual,
                'next_step' => $next_step,
                'steps_collected' => $steps_collected,
                'less_next_step' => $less_next_step,
                'actual_next_step' => $actual_next_step,
                'percent_next_step' => $percent_next_step,
                'reward_already_collected' => $reward_already_collected,
                'isNewStep' => $is_new_step,
            ];

        };

        if(!empty($_POST)) {

            if(isset($_POST['_state'], $_POST['_error'])) {

                $error = $_POST['_error'];
                $state = (int)$_POST['_state'];

                if(!empty($error)) {
                    $error_msg[] = $error;
                }

                if($state === 1 && empty($session->get('has_voted'))) {
                    $session->set('has_voted', true);
                    $success_msg[] = 'Votre vote a bien été pris en compte!';

                    // SERVER PART
                    $user_server = $this->getDoctrine()->getRepository(players::class)->findOneBy(['pseudo' => $user->getUsername()]);
                    $user_server->setPj($user_server->getPj() + $reward_info()['ilyCoins_per_vote']);
                    $success_msg[] = 'Votre Compte a été crédité de ' . $reward_info()['ilyCoins_per_vote'] . ' IlyCoins';

                        // NEXT STEP
                        if($reward_info()['isNewStep'] === true) {
                            $user_server->setPb($user_server->getPb() + $reward_info()['unit_step_reward']);
                            $success_msg[] = 'Félicitation, vous avez atteint le palier des '
                                . ($reward_info()['actual']+1) . ' votes. Votre compte a été crédité de '
                                . $reward_info()['unit_step_reward'] . ' Don(s)';
                        }

                        $em = $this->getDoctrine()->getManager('server');
                        $em->persist($user_server);
                        $em->flush();

                    // SITE PART
                    $user->setVotes($user->getVotes() + 1);
                    $em = $managerRegistry->getManager();
                    $em->persist($user);
                    $em->flush();
                }

            }

        }else $session->remove('has_voted');

        $values = array();

        $repo = $this->getDoctrine()->getRepository(Users::class);
        $users = $repo->findAll();

        foreach($users as $user) {
            $values[$user->getUsername()] = $user->getVotes();
        }

        arsort($values);
        $values = array_slice($values, 0, 5);

        $site_list = [
            //SERVEUR PRIVE
            ['title' => 'Serveur Privé',
             'link' => 'https://serveur-prive.net/minecraft/ilyriagames-6851/vote',],
            //SERVEUR MINECRAFT
            ['title' => 'Serveur Minecraft',
             'link' => 'https://www.serveurs-minecraft.org/vote.php?id=59858',],
            //TOP SERVEURS
            ['title' => 'Top Serveurs',
             'link' => 'https://top-serveurs.net/minecraft/vote/ilyriagames',],
        ];

        if((int)$id_site > (count($site_list)-1)) $id_site = (count($site_list)-1);

        return $this->render('accueil/voter.html.twig', [
            'values' => $values,
            'error_msg' => $error_msg,
            'success_msg' => $success_msg,
            'site_list' => $site_list,
            'current_site' => $site_list[(int)$id_site],
            'current_site_id' => (int)$id_site,
            'reward_info' => $reward_info(),
        ]);
    }

    /**
     * @Route("/Conditions", name="cgu")
     */
    public function cgu() {

        return $this->render('accueil/cgu.html.twig', []);

    }

    /**
     * @Route("/support", name="support")
     */
    public function support($ticket_title, $ticket_id) {

        $repo_users_site = $this->getDoctrine()->getRepository(Users::class);
        $repo_ticket_category = $this->getDoctrine()->getRepository(TCategory::class);
        $repo_ticket = $this->getDoctrine()->getRepository(Ticket::class);
        $categories = $repo_ticket_category->findAll();

        $em_site = $this->getDoctrine()->getManager('default');

        // TICKET CREATE
        if(isset($_POST['ticket_submit'],
                $_POST['ticket_title'],
                $_POST['ticket_content']) && $this->getUser() !== null) {

            $ticket = new Ticket();
            $ticket->setCreateAt(new \DateTime());
            $ticket->setResolved(false);
            $user = $repo_users_site->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
            $user === null ? $this->redirectToRoute('security_login') : $user->addTicket($ticket);

            $ticket->setPriority($_POST['ticket_priority']);

            $category = $repo_ticket_category->find($_POST['ticket_category']);
            $category === null ? : $category->addTicket($ticket);
            $ticket->setTitle($_POST['ticket_title']);
            $ticket->setContent($_POST['ticket_content']);

            $em_site->persist($ticket);
            $em_site->persist($user);
            $em_site->persist($category);
            $em_site->flush();

            return $this->redirectToRoute('support', array('ticket_title' => $ticket->getTitle(), 'ticket_id' => $ticket->getId()));

        }

        $ticket = null;
        if($ticket_title != null && $ticket_id != null) $ticket = $repo_ticket->findOneBy(array('id' => $ticket_id, 'title' => $ticket_title));

        //TICKET MESSAGES
        if(isset($_POST['ticket_message_submit'],
                $_POST['ticket_message_content']) && $this->getUser() !== null) {

            $message = new TMessage();
            $message->setCreateAt(new \DateTime());
            $message->setContent($_POST['ticket_message_content']);
            $user = $repo_users_site->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
            $user === null ? $this->redirectToRoute('security_login') : $user->addTicketsMessage($message);

            $ticket->addTicketsMessage($message);


            $em_site->persist($message);
            $em_site->persist($user);
            $em_site->persist($ticket);
            $em_site->flush();

        }

        //TICKET RESOLVED
        if(isset($_POST['ticket_resolved_submit'])) {
            $ticket->setResolved(true);
            $em_site->persist($ticket);
            $em_site->flush();
        }

        return $this->render('accueil/support/support.html.twig', [
            'categories' => $categories,
            'ticket' => $ticket,
        ]);

    }

    /**
     * @Route("/bêta", name="bêta")
     */
    public function beta(Request $request) {

        $em = $this->getDoctrine()->getManager('server');
        $repo = $this->getDoctrine()->getRepository(beta::class);

        $beta = new beta();
        $form = $this->createForm(BetaFormType::class, $beta);

        $msg = [];

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($repo->findOneBy(['pseudo' => $beta->getPseudo()])){
                $msg['error'] = 'Ce pseudo a déjà envoyé une demande.';
            }else {
                $beta->setAccepted(false);
                $em->persist($beta);
                $em->flush();

                $msg['success'] = 'Votre demande a été enregistrée! Votre demande sera traitée, et vous serez informé prochainement.';
            }
        }

        return $this->render('accueil/bêta/bêta.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);

    }
}

