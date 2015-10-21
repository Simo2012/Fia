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
     * @Route("/mobile",
     *        name="site_home_mobile")
     * @Method("GET")
     */
    public function indexAction() 
    {
       $loManager = $this->getDoctrine()->getManager();
       //Récupération Route Envoyé
       $loRequest = $this->container->get('request');
       $loRouteName = $loRequest->get('_route');
       if ($loRouteName === 'site_home') {
                    //Recuperer Site Prenium
           /**$loMembreLogger = $this->container->get('sceau.site.home.home_prenium');**/
            $lsPrenium = $this->container->get('sceau.site.home.home_prenium');
            $lpPreniumSite = $lsPrenium->getPreniumSite();
                    //Recuperer les categories disponibles
           $loCategories = $loManager->getRepository('SceauBundle:Categorie')->getActifCategories();
           /** Envoyer vars la page Home **/
                    //Recuperer la dérniére newsletter
           $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(1);
           
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('categories' => $loCategories,
               'siteprenium' => $lpPreniumSite ,'menu' => 'home', 'newsletters' => $loNewletters));
       } elseif ($loRouteName === 'site_operation_detail') {
           /** Envoyer vers la page fonctionnement **/
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('menu' => 'operation'));
       } elseif ($loRouteName === 'site_operation_news') {
                    //récuperer les 3 derniére newsletter
           $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(3);
           /** Envoyer vers la page NewsLetter **/
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('newsletters' => $loNewletters, 'menu' => 'newsletter'));
       } elseif ($loRouteName === 'site_home_mobile') {
           /** Envoyer vers la page Mobile **/
           return $this->render("SceauBundle:Site/Home:index.html.twig", array('menu' => 'mobile'));
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
                $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(3);
                /** Envoyer vers la page NewsLetter **/
                $this->get('session')->set('confirmation',$loNewletters);
                return $this->render("SceauBundle:Site/Home:index.html.twig", array('newsletters' => $loNewletters, 'menu' => 'newsletter', 'user' => $loUser));
            }
        }
        return $this->render("SceauBundle:Site/Security:test.html.twig"); 
    }
}
