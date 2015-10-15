<?php

namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Form\Type\Site\User\RegisterType;
/**
 * Contrôleur Home : pages relatives aux home de site web
 *
 * <pre>
 * Mohammed 06/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Site Sceau
 */
class HomeController extends Controller
{
    /**
     * Afficher la page home.
     *
     * @Route("/", name="site_home")
     * @Route("/operation",
     *     name="site_operation_detail")
     * @Route("/newsletter",
     *     name="site_operation_news")
     * @Method("GET")
     */
    public function indexAction() 
    {
       $loManager = $this->getDoctrine()->getManager();
       //Récupération Route Envoyé
       $loRequest = $this->container->get('request');
       $loRouteName = $loRequest->get('_route');
       if (preg_match('/site_home/i', $loRouteName)) {
                    //Recuperer les categories disponibles
           $loCategories = $loManager->getRepository('SceauBundle:Categorie')->getActifCategories();
           /** Envoyer vars la page Home **/
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('categories' => $loCategories, 'menu' => 'home'));
       } elseif (preg_match('/site_operation_detail/i', $loRouteName)) {
           /** Envoyer vers la page fonctionnement **/
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('menu' => 'operation'));
       } elseif (preg_match('/site_operation_news/i', $loRouteName)) {
                    //récuperer les 3 derniére newsletter
           $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters();
           /** Envoyer vers la page NewsLetter **/
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('newsletters' => $loNewletters, 'menu' => 'newsletter'));
       } else {
           return null;
       }
    }
    
     /**
     * Enregistrer la newsletter.
     *
     * @Route("/newsletter/subcribe",
     *     name="site_home_subcribe_newsletter")
     * @Method("POST")
     */
    public function registrenewsletterAction(Request $poRequest) {
        $loManager = $this->getDoctrine()->getManager();
        if ($poRequest->isMethod('POST')) {
            $loEmail = $poRequest->get('email'); 
            $loUser = $loManager->getRepository('SceauBundle:Membre')->getByMail($loEmail);
            if (empty($loUser)) {
                $loUser = new Membre();
                $loForm = $this->createForm(new RegisterType(), $loUser);
                return $this->render(
                                        'SceauBundle:Site/Home:index.html.twig',
                                        array(
                                                'form' => $loForm->createView(),
                                                'menu' => 'register',
                                                'redirect' => 'newsletter'
                                            )
                                    );
            } else {
                $loUser->setNewsletter(true);
                $loManager->flush();
                $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters();
                /** Envoyer vers la page NewsLetter **/
                $this->get('session')->set('confirmation',$loNewletters);
                return $this->render("SceauBundle:Site/Home:index.html.twig", array('newsletters' => $loNewletters, 'menu' => 'newsletter', 'user' => $loUser));
            }
        }
        return $this->render("SceauBundle:Site/Security:test.html.twig"); 
    }


    /**
     * List all published Articles.
     *
     * @Route("/presse", name="presse")
     * @Method("GET")
     */
    public function presseAction(Request $request)
    {
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $articlePresses = $articlePresseRepo->findBy(array(), array('date' => 'ASC'));

        return $this->render('SceauBundle:Site/Presse:index.html.twig', array('articlePresses' => $articlePresses));
    }

}
