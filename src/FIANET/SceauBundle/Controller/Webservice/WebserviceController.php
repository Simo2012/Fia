<?php

namespace FIANET\SceauBundle\Controller\Webservice;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebserviceController extends Controller
{
    /**
     * Valide et réceptionne un flux XML d'un client.
     * La validation est légère pour rendre rapidement la main au client. La validatin lourde se fait de manière
     * asynchrone à l'étape suivante du processus.
     *
     * @Route("/send_rating", name="ws_send_rating")
     * @Method("POST")
     *
     * @param Request $request Instance de Request
     *
     * @return Response Instance de Response
     */
    public function sendRatingAction(Request $request)
    {
        try {
            $this->get('fianet_sceau.flux')->inserer(
                $request->request->get('SiteID'),
                $request->request->get('XMLInfo'),
                $request->request->get('CheckSum'),
                $request->getClientIp()
            );

            $type = 'OK';
            $detail = 'Réception OK';

        } catch (Exception $e) {
            $type = 'KO';
            $detail = $e->getMessage();
        }

        $reponse = new Response();
        $reponse->headers->set('Content-Type', 'application/xml');

        return $this->render(
            'FIANETSceauBundle:Webservice:send_rating.xml.twig',
            array('type' => $type, 'detail' => $detail),
            $reponse
        );
    }

    /**
     * Affiche le schéma XML pour le webservice ws_send_rating.
     *
     * @Route("/send_rating/schema", name="ws_send_rating_schema")
     * @Method("GET")
     *
     * @return Response Instance de Response
     */
    public function sendRatingSchemaAction()
    {
        $reponse = new Response();
        $reponse->headers->set('Content-Type', 'application/xml');

        return $this->render(
            'FIANETSceauBundle:Webservice:send_rating_schema.xsd.twig',
            array(),
            $reponse
        );
    }
}
