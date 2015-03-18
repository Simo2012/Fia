<?php

namespace FIANET\SceauBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FIANET\SceauBundle\Entity\FluxStatut;

class LoadFluxStatutData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $fluxStatut = new FluxStatut();
        $fluxStatut->setLibelle('A traiter');
        $manager->persist($fluxStatut);

        $fluxStatut = new FluxStatut();
        $fluxStatut->setLibelle('En cours de traitement');
        $manager->persist($fluxStatut);

        $fluxStatut = new FluxStatut();
        $fluxStatut->setLibelle('Traité et valide');
        $manager->persist($fluxStatut);

        $fluxStatut = new FluxStatut();
        $fluxStatut->setLibelle('Traité et invalide');
        $manager->persist($fluxStatut);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 150;
    }
}
