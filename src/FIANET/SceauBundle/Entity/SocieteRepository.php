<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\EntityRepository;
use DateTime;
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;

class SocieteRepository extends EntityRepository
{
    /**
     * Récupère l'ensemble des sites d'une société et l'ensemble des types de questionnaires utilisés par ces sites.
     *
     * @param integer $id Identifiant de la société
     * @param  string $locale Locale de la requête
     *
     * @return Societe Instance de Societe
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function infosSocieteEtSitesLies($id, $locale)
    {
        $qb = $this->createQueryBuilder('so');

        $qb->join('so.sites', 'si')
            ->addSelect('si')
            ->join('si.administrationType', 'ads')
            ->addSelect('ads')
            ->join('si.questionnairePersonnalisations', 'siqp')
            ->addSelect('siqp')
            ->join('siqp.questionnaireType', 'qt')
            ->addSelect('qt')
            ->where('so.id = :id')
            ->setParameter('id', $id)
            ->andwhere('siqp.dateDebut <= CURRENT_DATE()')
            ->andWhere('siqp.dateFin IS NULL OR siqp.dateFin > CURRENT_DATE()')
            ->orderBy('si.nom', 'ASC')
            ->addOrderBy('qt.libelle', 'ASC');

        return $qb->getQuery()
            ->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale)
            ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker')
            ->useQueryCache(true)->useResultCache(true)->setResultCacheLifetime(86400)
            ->getSingleResult();
    }
}
