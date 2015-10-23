<?php

namespace SceauBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Indice;
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
     * Retourne la valeur d'un indice pour un site.
     *
     * @param int $indice_id Identifiant de l'indice
     * @param int $site_id Identifiant du site
     */
    public function getIndice($indice_id, $site_id)
    {
        /** @var Indice $indice */
        $indice = $this->em->getRepository('SceauBundle:Indice')->getIndice($indice_id);

        $valeurIndice = $this->em->getRepository('SceauBundle:QuestionnaireReponse')
            ->getValeurIndice($indice, $site_id);
    }
}
