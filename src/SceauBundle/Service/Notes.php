<?php

namespace SceauBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Indice;
use SceauBundle\Entity\IndiceType;
use SceauBundle\Entity\QuestionnaireType;

class Notes
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Méthode qui permet de récupérer pour un type de questionnaire les identifiants des réponses ou les bornes de
     * notation liés à un/des indicateur(s).
     *
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param array $indicateurs Tableau contenant les indicateurs souhaités (vert, jaune, rouge ou gris)
     *
     * @return array Tableau contenant 2 index : 'reponses' => les identifiants des réponses ou bornes de notations
     *     liées aux indicateurs choisis, 'nullable' => booléen indiquant si l'indicateur gris a été choisi
     */
    public function listeReponsesIndicateursPourQuestionnaireType(QuestionnaireType $questionnaireType, $indicateurs)
    {
        $listeReponses['reponses'] = [];
        $listeReponses['nullable'] = false;

        if ($questionnaireType->getParametrage()['indicateur']['type'] == 'reponse_id') {
            if (in_array('vert', $indicateurs)) {
                $listeReponses['reponses'] = array_merge(
                    $listeReponses['reponses'],
                    $questionnaireType->getParametrage()['indicateur']['vert']
                );
            }

            if (in_array('jaune', $indicateurs)) {
                $listeReponses['reponses'] = array_merge(
                    $listeReponses['reponses'],
                    $questionnaireType->getParametrage()['indicateur']['jaune']
                );
            }

            if (in_array('rouge', $indicateurs)) {
                $listeReponses['reponses'] = array_merge(
                    $listeReponses['reponses'],
                    $questionnaireType->getParametrage()['indicateur']['rouge']
                );
            }

        } else {
            if (in_array('vert', $indicateurs)) {
                $listeReponses['reponses'][] =
                    ['min' => $questionnaireType->getParametrage()['indicateur']['vert']['min'],
                    'max' => $questionnaireType->getParametrage()['indicateur']['vert']['max']];
            }

            if (in_array('jaune', $indicateurs)) {
                $listeReponses['reponses'][] =
                    ['min' => $questionnaireType->getParametrage()['indicateur']['jaune']['min'],
                    'max' => $questionnaireType->getParametrage()['indicateur']['jaune']['max']];
            }

            if (in_array('rouge', $indicateurs)) {
                $listeReponses['reponses'][] =
                    ['min' => $questionnaireType->getParametrage()['indicateur']['rouge']['min'],
                    'max' => $questionnaireType->getParametrage()['indicateur']['rouge']['max']];
            }
        }

        if (in_array('gris', $indicateurs)) {
            $listeReponses['nullable'] = true;
        }

        return $listeReponses;
    }

    /**
     * Retourne la valeur d'un indice pour un site. En fonction de l'indice, la valeur retournée peut être un entier ou
     * un flottant. S'il n'y a aucune donnée pour l'indice, retourne null.
     *
     * @param int $indice_id Identifiant de l'indice
     * @param int $site_id Identifiant du site
     *
     * @return float|int|null
     *
     * @throws \Exception Si l'indice demandé n'existe pas
     */
    public function getIndice($indice_id, $site_id)
    {
        /** @var Indice $indice */
        $indice = $this->em->getRepository('SceauBundle:Indice')->getIndice($indice_id);

        if (!$indice) {
            throw new \Exception("L'indice $indice_id n'existe pas.");
        }

        $valeurBrut = $this->em->getRepository('SceauBundle:Indice')->getValeurIndice($indice, $site_id);

        if ($indice->getIndiceType()->getId() == IndiceType::MOYENNE) {
            $valeurIndice = round($valeurBrut, 1);

        } elseif ($indice->getIndiceType()->getId() == IndiceType::POURCENTAGE) {
            $indiceNbAvis = $this->em->getRepository('SceauBundle:Indice')
                ->getIndice($indice->getQuestionnaireType()->getParametrage()['nbAvis']);

            $nbAvis = $this->em->getRepository('SceauBundle:Indice')->getValeurIndice($indiceNbAvis, $site_id);

            if ($nbAvis != 0) {
                $valeurIndice = round(($valeurBrut * 100) / $nbAvis, 1);

            } else {
                $valeurIndice = null;
            }

        } else {
            return $valeurBrut;
        }

        return $valeurIndice;
    }
}
