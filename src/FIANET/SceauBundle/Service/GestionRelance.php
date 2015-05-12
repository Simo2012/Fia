<?php

namespace FIANET\SceauBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\Langue;
use FIANET\SceauBundle\Entity\Relance;
use FIANET\SceauBundle\Entity\Site;

class GestionRelance
{
    private $em;
    private $delaiJoursRelance;

    public function __construct(EntityManager $em, $delaiJoursRelance)
    {
        $this->em = $em;
        $this->delaiJoursRelance = $delaiJoursRelance;
    }

    /**
     * Calcule la période pendant laquelle les questionnaires qui n'ont pas été répondus peuvent être relancés.
     * Actuellement seuls les questionnaires de la semaine-2 peuvent l'être.
     * Calcul du lundi et du dimanche de la semaine-2, par exemple on est le 22/11/2012 (jeudi) :
     * 22 - 4 (jeudi) = 18 -> + 1 car date('N') de lundi égal 1, il y a un décalage de 1 soit 19
     *
     * @return array Tableau contenant 2 clés : 'dateDebut' => DateTime du 1er jour de la période,
     *     'dateFin' => DateTime du dernier jour de la période
     */
    public function calculerPeriode()
    {
        $tsDernierLundi = mktime(0, 0, 0, date('m'), date('d') - date('N') + 1, date('Y'));
        $tsDateDebut = $tsDernierLundi - ($this->delaiJoursRelance * 24 * 60 * 60);
        $tsDateFin = $tsDateDebut + (6 * 24 * 60 * 60);

        return array(
            'dateDebut' => new DateTime(date('Y-m-d', $tsDateDebut) . ' 00:00:00'),
            'dateFin' => new DateTime(date('Y-m-d', $tsDateFin) . ' 23:59:59')
        );
    }

    /**
     * Active ou désactive les relances automatiques pour un site et pour une langue.
     * Dans le cas d'une activation :
     *  - S'il existe un modèle de relance validé, alors c'est ce modèle qui va être automatisé.
     *  - S'il n'existe pas de modèle de relance validé, une relance par défaut validé est créée et automatisée.
     * Dans le cas d'une désactivation :
     * - On récupère le modèle de relance validé et on désautomatise.
     *
     * @param bool $activer true => active les relances auto, false => désactive les relances auto
     * @param Site $site Instance de Site
     * @param Langue $langue Instance de Langue
     */
    private function changerAutomatisation($activer, Site $site, $langue)
    {
        $relance = $this->em->getRepository('FIANETSceauBundle:Relance')->relanceValidee($site, $langue);

        if ($activer) {
            if ($relance) {
                $relance->setAuto(true);
                $this->em->flush();

            } else {
                $relance = new Relance();
                $relance->setDateCreation(new DateTime());
                $relance->setAuto(true);
                $relance->setRelanceStatut($this->em->getRepository('FIANETSceauBundle:RelanceStatut')->find(1));
                $relance->setSite($site);
                $relance->setLangue($langue);

                $this->em->persist($relance);
                $this->em->flush();
            }

        } else {
            if ($relance) {
                $relance->setAuto(false);
                $this->em->flush();
            }
        }
    }

    /**
     * Active les relances automatiques pour un site et pour une langue.
     *
     * @param Site $site Instance de Site
     * @param Langue $langue Instance de Langue
     */
    public function automatiser(Site $site, $langue)
    {
        $this->changerAutomatisation(true, $site, $langue);
    }

    /**
     * Désactive les relances automatiques pour un site et pour une langue.
     *
     * @param Site $site Instance de Site
     * @param Langue $langue Instance de Langue
     */
    public function desautomatiser(Site $site, $langue)
    {
        $this->changerAutomatisation(false, $site, $langue);
    }
}
