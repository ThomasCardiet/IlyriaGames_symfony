<?php

namespace App\Controller;

use App\Entity\Main\FCategory;
use App\Entity\Main\FMessage;
use App\Entity\Main\FSubcategory;
use App\Entity\Main\FTopic;
use App\Entity\Main\Notification;
use App\Entity\Main\Users;
use App\Entity\Server\players;
use App\Form\CreateTopicType;
use FOS\CKEditorBundle\Config\CKEditorConfiguration;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use JBBCode\DefaultCodeDefinitionSet;
use JBBCode\Parser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends AbstractController
{

    public function index($category_id, $sub_category_id, $topic_title, $topic_id, $page_nb, Request $request)
    {

        $topic_title = urldecode($topic_title);

        $repo_categories = $this->getDoctrine()->getRepository(FCategory::class);
        $repo_subcategories = $this->getDoctrine()->getRepository(FSubcategory::class);
        $repo_topic = $this->getDoctrine()->getRepository(FTopic::class);
        $repo_userServer = $this->getDoctrine()->getRepository(players::class);
        $repo_userSite = $this->getDoctrine()->getRepository(Users::class);
        $repo_messages = $this->getDoctrine()->getRepository(FMessage::class);

        $em_site = $this->getDoctrine()->getManager('default');

        $em = $this->getDoctrine()->getManager('default');

        $categories = $repo_categories->findAll();
        $sub_categories = $repo_subcategories->findAll();

        // SEARCH BUTTON

        $search_value = null;
        if(isset($_POST['buttonSearch']) && isset($_POST['textSearch']) && strlen($_POST['textSearch']) > 0) {
            $search_value = $_POST['textSearch'];
        }

        if($search_value != null) $categories = $repo_categories->search($search_value);

        // SUB CATEGORIES
        if($category_id != null && $sub_category_id == null) {

            $category = $repo_categories->find($category_id);
            $sub_categories = $category->getSubcategories();

            if($search_value != null) $sub_categories = $repo_subcategories->search($search_value, $category_id);

            return $this->render('forum/forum_subcategories.html.twig', [
                'category' => $category,
                'sub_categories' => $sub_categories,
            ]);

        }

        // TOPICS
        if($category_id != null && $sub_category_id != null && ( $topic_title == null || $topic_id == null)) {

            $category = $repo_categories->find($category_id);
            $sub_category = $repo_subcategories->find($sub_category_id);
            $topics = $sub_category->getTopics();

            if($search_value != null) $topics = $repo_topic->search($search_value, $sub_category_id);

            return $this->render('forum/forum_topics.html.twig', [
                'sub_category' => $sub_category,
                'category' => $category,
                'topics' => $topics,
            ]);

        }

        // VIEW TOPIC
        if($category_id != null && $sub_category_id != null && $topic_title != null && $topic_id != null) {

            $category = $repo_categories->find($category_id);
            $sub_category = $repo_subcategories->find($sub_category_id);
            $topic = $repo_topic->find($topic_id);
            if(empty($topic)) return $this->render('forum/forum.html.twig', [
                'categories' => $categories,
                'sub_categories' => $sub_categories,
            ]);
            $user_server = $repo_userServer->findOneBy(array('pseudo' => $topic->getOwner()->getUsername()));

            $parser = new Parser();
            $parser->addBBCode("center", '<div align="center">{param}</div>');
            $parser->addBBCode("left", '<div align="left">{param}</div>');
            $parser->addBBCode("right", '<div align="right">{param}</div>');
            $parser->addBBCode("size", "<span style='font-size: {option};'>{param}</span>", true);
            $parser->addBBCode("list", "<ul>{param}</ul>");
            $parser->addBBCode("*", "<li>{param}</li>");
            $parser->addBBCode("url", "<a href='{option}'>{param}</a>", true);
            $parser->addBBCode('s', "<s>{param}</s>");

            $parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
            $parser->parse($topic->getContent());

            $content = nl2br($parser->getAsHTML());

            //PAGE SYSTEM
            $nb_response_max_page = 5;
            if($page_nb == null || $page_nb < 1) $page_nb = 1;

            $response_start = ($page_nb-1) * $nb_response_max_page;

            $responses = $repo_messages->findBy(array('topic' => $topic_id), null, $nb_response_max_page, $response_start);

            $page_count = ceil(count($repo_messages->findBy(array('topic' => $topic_id)))/$nb_response_max_page);
            if($page_count == 0) $page_count = 1;

            //TRAITEMENT REPONSE

            $response_content = null;
            $msg = null;

            if(isset($_POST['tresponse_submit'],$_POST['tresponse'])) {
                $response_content = htmlspecialchars($_POST['tresponse']);
                if($this->getUser() != null) {
                    if($response_content != null && strlen($response_content) > 0) {

                        $response = new FMessage();
                        $response->setContent($response_content)
                            ->setCreateAt(new \DateTime())
                            ->setOwner($repo_userSite->findOneBy(array('pseudo' => $this->getUser()->getUsername())))
                            ->setTopic($topic);

                        $em_site->persist($response);

                        // NOTIF SECTION
                        $notif = new Notification();
                        $notif->construct($topic->getOwner(), 0, [
                            'interlocutor' => $this->getUser()->getUsername(),
                            'topic' => $topic,
                        ], $request);
                        $em_site->persist($notif);

                        $em_site->flush();

                        $msg = "Réponse bien enregistrée.";

                        $response_content = null;

                    } else {
                        $msg = "Votre réponse ne doit pas être vide.";
                    }
                } else $msg = "Merci de vous connecter pour accéder à ceci.";
            }

            if(isset($_POST['delete_topic_submit'])) {

                $em_site->remove($topic);
                $em_site->flush();

                return $this->redirectToRoute('forum');
            }

            if(strcasecmp($topic_title, $topic->getTitle()) === -1) {
                return $this->render('forum/forum.html.twig', [
                    'categories' => $categories,
                    'sub_categories' => $sub_categories,
                ]);
            }

            return $this->render('forum/topic_view.html.twig', [
                'sub_category' => $sub_category,
                'category' => $category,
                'topic' => $topic,
                'user_server' => $user_server,
                'content' => $content,
                'msg' => $msg,
                'response_content' => $response_content,
                'responses' => $responses,
                'page_nb' => $page_nb,
                'response_start' => $response_start,
                'page_count' => $page_count,
                'repo_user_server' => $repo_userServer,
            ]);

        }

        // MAIN
        return $this->render('forum/forum.html.twig', [
            'categories' => $categories,
            'sub_categories' => $sub_categories,
            'repo_user_server' => $repo_userServer,
        ]);
    }

    public function create($category_id, Request $request)
    {

        if($category_id == null || $this->getUser() == null) return $this->redirectToRoute('forum');

        $repo_categories = $this->getDoctrine()->getRepository(FCategory::class);
        $repo_users = $this->getDoctrine()->getRepository(Users::class);
        $category = $repo_categories->find($category_id);

        $em_site = $this->getDoctrine()->getManager('default');

        $msg = null;

        $topic = new FTopic();


        $form = $this->createForm(CreateTopicType::class, $topic);
        $form->add('subcategory', EntityType::class, [
            'class' => FSubcategory::class,
            'choice_label' => 'name',
            'choices'  => $category->getSubcategories(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $topic->setCreateAt(new \DateTime())
                ->setOwnerNotif(false)
                ->setResolved(false);

            if(!empty($topic->getContent()) AND !empty($topic->getTitle())) {
                if(strlen($topic->getTitle()) <= 70) {

                    $user = $repo_users->findOneBy(array('pseudo' => $this->getUser()->getUsername()));
                    $user->addTopic($topic);
                    $em_site->persist($topic);
                    $em_site->persist($user);

                    $em_site->flush();

                    return $this->redirectToRoute('forum', array(
                        'category_id' => $category_id,
                        'sub_category_id' => $topic->getSubcategory()->getId(),
                        'topic_title' => $topic->get_url_custom_encode_title(),
                        'topic_id' => $topic->getId()));

                }else $msg = "La taille maximum du sujet est de 70";
            }else $msg = "Merci de remplir tous les champs";
        }

        return $this->render('forum/topic_create.html.twig', [
            'category' => $category,
            'msg' => $msg,
            'form' => $form->createView(),
        ]);
    }
}
