<?php namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Form\Type\Site\User\RegisterType;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Site\TicketQuestionType;
use SceauBundle\Entity\Ticket;

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
     * @Method("GET")
     */
    public function indexAction()
    {
        $loManager = $this->getDoctrine()->getManager();
        $loUser = null;
        if ($this->get('security.token_storage')->getToken() != null) {
            $loPseudoMembre = $this->get('security.token_storage')->getToken()->getUser();
            if ($loPseudoMembre != 'anon.') {
                $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
            }
        }
        //Recuperer Site Prenium
        /*         * $loMembreLogger = $this->container->get('sceau.site.home.home_prenium');* */
        $lsPrenium = $this->container->get('sceau.site.home.home_prenium');
        $lpPreniumSite = $lsPrenium->getPreniumSite();
        //Recuperer les categories disponibles
        $loCategories = $loManager->getRepository('SceauBundle:Categorie')->getActifCategories();
        /** Envoyer vars la page Home * */
        //Recuperer la dérniére newsletter
        $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(1);
        return $this->render("SceauBundle:Site/Home:index.html.twig", array('categories' => $loCategories,
                'siteprenium' => $lpPreniumSite, 'menu' => 'home', 'newsletters' => $loNewletters,
                'user' => $loUser));
    }

    /**
     * Pour switcher entre plusieurs action de la page home.
     *
     * @Route("/operation",
     *     name="site_operation_detail")
     * @Route("/newsletter",
     *     name="site_operation_news")
     * @Route("/mobile",
     *        name="site_home_mobile")
     * @Route("/whos",
     *        name="site_whos_fianet")
     * @Route("/mention_legales",
     *        name="site_fianet_mention")
     * @Route("/protection_donnees",
     *        name="site_fianet_protectiondonnes")
     * @Route("/CGU",
     *        name="site_fianet_cgu")
     * @Route("/politique_qualite",
     *        name="site_fianet_politique")
     * @Route("/Faq",
     *        name="site_fianet_faq")
     * @Method("GET")
     *
     */
    public function switchAction()
    {
        $loManager = $this->getDoctrine()->getManager();
        $loUser = null;
        $loNewletters = null;
        $loPseudoMembre = $this->get('security.token_storage')->getToken()->getUser();
        if ($loPseudoMembre != 'anon.') {
            $loUser = $loManager->getRepository('SceauBundle:Membre')->getByPseudo($loPseudoMembre);
        }
        //Récupération Route Envoyé
        $loRequest = $this->container->get('request');
        $loRouteName = $loRequest->get('_route');
        if ($loRouteName === 'site_operation_news') {
            $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(1);
        }
        return $this->render(
                "SceauBundle:Site/Home:index.html.twig", array('menu' => $loRouteName, 'newsletters' => $loNewletters, 'user' => $loUser)
        );
    }

    /**
     * Enregistrer la newsletter.
     *
     * @Route("/newsletter/subcribe",
     *     name="site_home_subcribe_newsletter")
     * @Method("POST")
     *
     * @param Request $poRequest
     *
     * @return Response
     */
    public function registrenewsletterAction(Request $poRequest)
    {
        $loManager = $this->getDoctrine()->getManager();
        if ($poRequest->isMethod('POST')) {
            $loEmail = $poRequest->get('email');
            $loUser = $loManager->getRepository('SceauBundle:Membre')->getByMail($loEmail);
            if (empty($loUser)) {
                $loUser = new Membre();
                $loForm = $this->createForm(new RegisterType(), $loUser);
                return $this->render(
                        'SceauBundle:Site/Home:index.html.twig', array(
                        'form' => $loForm->createView(),
                        'menu' => 'register',
                        'redirect' => 'newsletter'
                        )
                );
            } else {
                $loUser->setNewsletter(true);
                $loManager->flush();
                $loNewletters = $loManager->getRepository('SceauBundle:Newsletters')->getLastNewsLetters(3);

                /** Envoyer vers la page NewsLetter * */
                $this->get('session')->set('confirmation', 'OK');
                return $this->render(
                        "SceauBundle:Site/Home:index.html.twig", array('newsletters' => $loNewletters, 'menu' => 'site_operation_news', 'user' => $loUser)
                );
            }
        }
        return $this->render("SceauBundle:Site/Security:test.html.twig");
    }

    /**
     * List all published Articles.
     *
     * @Route("/presse", name="site_presse")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function presseAction()
    {
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $articlePresses = $articlePresseRepo->getAllArticlePresse();

        $articlePressesByMonths = array();

        //Group by month
        foreach ($articlePresses as $articlePresse) {
            $articlePressesDate = $articlePresse->getDate()->format('m-Y');
            if (isset($articlePressesByMonths[$articlePressesDate])) {
                $articlePressesByMonths[$articlePressesDate]['articles'][] = $articlePresse;
            } else {
                $articlePressesByMonths[$articlePressesDate]['articles'] = [$articlePresse];
                $articlePressesByMonths[$articlePressesDate]['date'] = $articlePresse->getDate();
            }
        }

        return $this->render(
                'SceauBundle:Site/Presse:index.html.twig', array('articlePressesByMonths' => $articlePressesByMonths)
        );
    }

    /**
     * Action pour telecharger politique
     *
     * @Route("/download_politique",
     *     name="site_politique_downald")
     * @Method("GET")
     */
    public function downloadPolitiqueAction()
    {

        $path = $this->get('kernel')->getRootDir() . "/../web/documents/";
        $content = file_get_contents($path . 'PolitiqueQualite-SCEAU.pdf');

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename=PolitiqueQualite-SCEAU.pdf');

        $response->setContent($content);
        return $response;
    }
    //

    /**
     * List all published Articles.
     *
     * @Route("/contact", name="site_contact")
     * @Method({"GET","POST"})
     */
    public function contactAction(Request $request)
    {
        $ticket = new Ticket();
        $template = null;

        $form = $this->createForm(new TicketQuestionType(), $ticket);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($form->has('submit') && $form->get('submit')->isClicked()) {
                $this->getDoctrine()->getManager()->persist($ticket);
                $this->getDoctrine()->getManager()->flush();
            }

            $template = $ticket->getType()->getTemplate();
        }

        return $this->render(
                'SceauBundle:Site/Contact:index.html.twig', [
                'form' => $form->createView(),
                'template' => $template,
                ]
        );
    }
}
