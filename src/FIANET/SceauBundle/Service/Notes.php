<?php

namespace FIANET\SceauBundle\Service;

use FIANET\SceauBundle\Entity\QuestionnaireType;
use Symfony\Component\Config\Definition\Exception\Exception;

class Notes
{
    /**
     * Méthode qui permet de récupérer pour type un questionnaire les identifiants des réponses pour un/des
     * indicateur(s).
     *
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param array $indicateurs Tableau contenant les indicateurs souhaités (vert, jaune, rouge ou gris)
     *
     * @return array Tableau contenant 2 index : 'reponses' => les identifiants des réponses liées aux indicateurs
     *               choisis, 'nullable' => booléen indiquant si l'indicateur gris a été choisi
     *
     * @throw Exception Le QuestionnaireType ne possède pas de paramètrage
     */
    public function getReponsesIDIndicateursPourQuestionnaireType(QuestionnaireType $questionnaireType, $indicateurs)
    {
        if (!$questionnaireType->getParametrage()) {
            throw new Exception('Le type de questionnaire n°' . $questionnaireType->getId() . ' n\'est pas paramétré');
        }

        $listeReponses['reponses'] = array();
        $listeReponses['nullable'] = false;

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

        if (in_array('gris', $indicateurs)) {
            $listeReponses['nullable'] = true ;
        }

        return $listeReponses;
    }
}
