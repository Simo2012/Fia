<?php
namespace SceauBundle\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SceauBundle\Entity\Membre;
use SceauBundle\Form\Type\Site\User\RegisterType;

/**
 * Contrôleur Tombola : pages relatives aux Tombola de site web
 *
 * <pre>
 * Mohammed 20/10/2015 Création
 * </pre>
 * @author Mohammed
 * @version 1.0
 * @package Site Sceau
 */
class TombolaController extends Controller
{
    /**
     * Action pour l'appel du Participation a tombola
     * @Route("/tombola",
     *     name="site_home_tombola")
     * @Method("GET")
     */
    public function callTombolaAction()
    {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser, array('attr' => array('tombola' => 1)));
        $loTombola = $this->container->get('sceau.site.user.user_tombola');
        $loWinner = $loTombola->getWinner();
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'tombola',
                'call' => 1,
                'redirect' => '',
                'winners' => $loWinner
            )
        );
    }

    /**
     * Action pour l'appel du Participation a tombola
     * @Route("/tombola/login",
     *     name="site_tombola_login")
     * @Method("POST")
     */
    public function loginTombolaAction(Request $poRequest)
    {
        if ($poRequest->isMethod('POST')) {
            $loEmail = $poRequest->get('email');
            $loPassword = $poRequest->get('password');
            if (!empty($loPassword) && !empty($loEmail)) {
                try {
                    $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                    $loUser = $loMembreLogger->logUser($loEmail, $loPassword);
                    $loNumberParticipation = $loMembreLogger->particpateTombola($loUser, 1);
                    $loTombola = $this->container->get('sceau.site.user.user_tombola');
                    $loWinner = $loTombola->getWinner();
                    return $this->render(
                        'SceauBundle:Site/Home:index.html.twig',
                        array(
                            'participation' => $loNumberParticipation,
                            'menu' => 'tombola',
                            'call' => 2,
                            'winners' => $loWinner
                        )
                    );
                } catch (\Exception $e) {
                    return $this->errorAction($e->getMessage(), 'tombola_login', '1');
                }
            }
        }
        return $this->forward('SceauBundle:Site/Home:callRegisterTombola');
    }

    /**
     * Action pour l'appel du Participation a tombola
     * @Route("/tombola/register",
     *     name="site_member_register_tombola")
     * @Method("POST")
     *
     * @param Request $poRequest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function regitreTombolaAction(Request $poRequest)
    {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser);
        $loManager = $this->getDoctrine()->getManager();
        // $recaptcha = $this->createForm($this->get('security.authentication.listener.form'));
        if ($poRequest->isMethod('POST')) {
            $loForm->handleRequest($poRequest);
            $loFields = $poRequest->get('site_member_register');
            if ($loFields['email']['first'] == $loFields['email']['second']) {
                try {
                    if ($poRequest->get('g-recaptcha-response') == '') {
                        return $this->errorAction('Le captcha est obligatoire', 'tombola', '1');
                    }
                    $loIdAvatar = $poRequest->get('AvatarID');


                    $loMembreLogger = $this->container->get('sceau.site.user.user_logger');
                    $loAvatar = $loMembreLogger->saveAvatar($loIdAvatar);
                    $loEmails = $loMembreLogger->saveEmail($loFields);
                    $loCoordonnes = $loMembreLogger->saveCoordonnées($loFields);
                    $loUser->setAvatar($loAvatar);
                    $loUser->addEmail($loEmails);
                    $loUser->setCoordonnee($loCoordonnes);
                    $loUser = $loMembreLogger->registerUser($loUser, $loFields['email']['first'], true);
                    $loNumberParticipation = $loMembreLogger->particpateTombola($loUser, 1);
                    $loTombola = $this->container->get('sceau.site.user.user_tombola');
                    $loWinner = $loTombola->getWinner();
                    return $this->render(
                        'SceauBundle:Site/Home:index.html.twig',
                        array(
                            'participation' => $loNumberParticipation,
                            'menu' => 'tombola',
                            'call' => 2,
                            'winners' => $loWinner
                        )
                    );
                } catch (\Exception $e) {
                    dump($e->getMessage());
                    return $this->errorAction($e->getMessage(), 'tombola', '1');
                }
            } else {
                return $this->errorAction('les adresses emails dois etres pareils', 'tombola', '1');
            }
        }
        return $this->forward('SceauBundle:Site/Home:callRegisterTombola');
    }

    /**
     * A l'appele du fonction lors d'erreur Login
     */
    public function errorAction($poError, $poAction, $poCall)
    {
        $loUser = new Membre();
        $loForm = $this->createForm(new RegisterType(), $loUser, array('attr' => array('tombola' => 1)));
        $loTombola = $this->container->get('sceau.site.user.user_tombola');
        $loWinner = $loTombola->getWinner();
        return $this->render(
            'SceauBundle:Site/Home:index.html.twig',
            array(
                'form' => $loForm->createView(),
                'menu' => 'tombola',
                'winners' => $loWinner,
                'user' => null,
                'error' => $poError,
                'errorType' => $poAction,
                'redirect' => '',
                'call' => $poCall
            )
        );
    }
}
