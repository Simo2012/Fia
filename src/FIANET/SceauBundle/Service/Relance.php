<?php

namespace FIANET\SceauBundle\Service;

use DateTime;

class Relance
{
    /**
     * Calcule la période pendant laquelle les questionnaires qui n'ont pas été répondus peuvent être relancés.
     * Actuellement seuls les questionnaires de la semaine-2 peuvent l'être.
     * Calcul du lundi et du dimanche de la semaine-2, par exemple on est le 22/11/2012 (jeudi) :
     * 22 - 4 (jeudi) = 18 -> + 1 car date('N') de lundi égal 1, il y a un décalage de 1 soit 19
     *
     * @return array Tableau contenant 2 clés : 'dateDebut' => DateTime du 1er jour de la période,
     *     'dateFin' => DateTime du dernier jour de la période
     */
    public function calculerPeriodeRelance()
    {
        $tsDernierLundi = mktime(0, 0, 0, date('m'), date('d') - date('N') + 1, date('Y'));
        $tsDateDebut = $tsDernierLundi - (14 * 24 * 60 * 60);
        $tsDateFin = $tsDateDebut + (6 * 24 * 60 * 60);

        return array(
            'dateDebut' => new DateTime(date('Y-m-d', $tsDateDebut) . ' 00:00:00'),
            'dateFin' => new DateTime(date('Y-m-d', $tsDateFin) . ' 23:59:59')
        );
    }
}
