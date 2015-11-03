<?php

namespace SceauBundle\Entity\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use SceauBundle\Cache\Cache;
use SceauBundle\Entity\Indice;
use SceauBundle\Entity\IndiceType;

class IndiceRepository extends EntityRepository
{
    /**
     * Permet de récupérer une instance d'indice avec tout son paramètrage.
     *
     * @param int $id Identifiant de l'indice
     *
     * @return Indice Instance d'Indice
     */
    public function getIndice($id)
    {
        $qb = $this->createQueryBuilder('i');

        $qb->innerJoin('i.indiceType', 'it')
            ->addSelect('it')
            ->innerJoin('i.questionnaireType', 'qt')
            ->addSelect('qt')
            ->leftJoin('i.question', 'q')
            ->addSelect('q')
            ->leftJoin('i.reponse', 'r')
            ->addSelect('r')
            ->leftJoin('i.indicesSecondaires', 'ise')
            ->addSelect('ise')
            ->where('i.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->useResultCache(true, Cache::LIFETIME_1J, "indice_$id")->getOneOrNullResult();
    }

    /**
     * Ajoute les restrictions nécessaires à un QueryBuilder pour sélectionner uniquement les questionnaires répondus
     * dans la période de notation.
     *
     * @param QueryBuilder $qb Instance de QueryBuilder
     *
     * @return QueryBuilder
     */
    private function restrictionPeriodeNotation(QueryBuilder $qb)
    {
        $dateLimite = new \DateTime();
        $dateLimite->sub(new \DateInterval('P6M'));

        $qb->andWhere($qb->expr()->gte('q.dateReponse', ':dateLimite'))
            ->setParameter('dateLimite', $dateLimite, Type::DATE);

        return $qb;
    }

    /**
     * Retourne un QueryBuilder pour récupérer la moyenne des réponses d'un indice.
     *
     * @param Indice $indice Instance d'indice
     *
     * @return QueryBuilder
     */
    private function getQueryIndiceMoyenne(Indice $indice)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select($qb->expr()->avg('qr.note'))
            ->from('SceauBundle:QuestionnaireReponse', 'qr')
            ->innerJoin('qr.questionnaire', 'q');

        if ($indice->getIndicesSecondaires()->isEmpty()) {
            $qb->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('qr.question', $indice->getQuestion()->getId()),
                    $qb->expr()->eq('qr.reponse', $indice->getReponse()->getId())
                )
            );

        } else {
            $orXquestionReponse = $qb->expr()->orX();

            foreach ($indice->getIndicesSecondaires() as $indiceSecondaire) {
                $orXquestionReponse->add($qb->expr()->andX(
                    $qb->expr()->eq('qr.question', $indiceSecondaire->getQuestion()->getId()),
                    $qb->expr()->eq('qr.reponse', $indiceSecondaire->getReponse()->getId())
                ));
            }
            $qb->where($orXquestionReponse);
        }

        $qb = $this->restrictionPeriodeNotation($qb);

        return $qb;
    }

    /**
     * Retourne un QueryBuilder pour récupérer le nombre de sélection d'une réponse.
     *
     * @param Indice $indice Instance d'indice
     *
     * @return QueryBuilder
     */
    private function getQueryIndiceNbReponse(Indice $indice)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select($qb->expr()->count('qr.id'))
            ->from('SceauBundle:QuestionnaireReponse', 'qr')
            ->innerJoin('qr.questionnaire', 'q')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('qr.question', $indice->getQuestion()->getId()),
                $qb->expr()->eq('qr.reponse', $indice->getReponse()->getId())
            ));

        $qb = $this->restrictionPeriodeNotation($qb);

        return $qb;
    }

    /**
     * Retourne un QueryBuilder pour récupérer le nombre de questionnaire répondu.
     *
     * @param bool $periode Si période vaut true, le calcul se fait sur la période de notation (par défaut vaut false)
     *
     * @return QueryBuilder
     */
    private function getQueryIndiceNbAvis($periode = false)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select($qb->expr()->count('q.id'))
            ->from('SceauBundle:Questionnaire', 'q')
            ->where($qb->expr()->isNotNull('q.dateReponse'));

        if ($periode) {
            $qb = $this->restrictionPeriodeNotation($qb);
        }

        return $qb;
    }

    /**
     * Retourne la valeur d'un indice pour un site.
     *
     * @param Indice $indice Instance de Indice
     * @param int $site_id Identifiant du site
     *
     * @return int|float
     */
    public function getValeurIndice(Indice $indice, $site_id)
    {
        if ($indice->getIndiceType()->getId() == IndiceType::MOYENNE) {
            $qb = $this->getQueryIndiceMoyenne($indice);

        } elseif ($indice->getIndiceType()->getId() == IndiceType::POURCENTAGE) {
            $qb = $this->getQueryIndiceNbReponse($indice);

        } elseif ($indice->getIndiceType()->getId() == IndiceType::NB_AVIS_PERIODE) {
            $qb = $this->getQueryIndiceNbAvis(true);

        } else {
            $qb = $this->getQueryIndiceNbAvis();
        }

        $qb->andWhere($qb->expr()->eq('q.site', $site_id))
            ->andWhere($qb->expr()->eq('q.questionnaireType', $indice->getQuestionnaireType()->getId()));

        $cleCache = 'indice_' . $indice->getId() . '_' . $site_id . '_' . $indice->getQuestionnaireType()->getId();
        $lifetimeCache = Cache::lifetimeUntilMidnight();

        return $qb->getQuery()->useResultCache(true, $lifetimeCache, $cleCache)->getSingleScalarResult();
    }
}
