<?php

namespace FIANET\SceauBundle\Controller\Extranet;

use FIANET\SceauBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommunController extends Controller
{
    /**
     * Met à jour la session avec le site sélectionné par l'utilisateur.
     *
     * @Route("/site_selectionne/{id}", name="extranet_commun_site_selectionne", options={"expose"=true})
     * @Method("GET")
     *
     * @param Request $request Instance de Request
     * @param Site $site Instance de Site
     *
     * @return JsonResponse Retourne true si OK sinon false
     */
    public function selectionSiteAction(Request $request, Site $site)
    {
        /* On teste que l'utilisateur a bien accès au site demandé */
        $listeSitesAutorises = array();
        foreach ($this->getUser()->getSociete()->getSites() as $siteAutorise) {
            $listeSitesAutorises[] = $siteAutorise->getId();
        }

        if (in_array($site->getId(), $listeSitesAutorises)) {
            $request->getSession()->set('siteSelectionne', $site);
            return new JsonResponse(true);

        } else {
            return new JsonResponse(false);
        }
    }
}
