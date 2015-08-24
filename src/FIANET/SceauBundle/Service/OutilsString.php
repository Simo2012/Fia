<?php

namespace FIANET\SceauBundle\Service;

use Collator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Service contenant des outils qui permettent de manipuler des chaines de caractères.
 */
class OutilsString
{
    private $translator;
    private $collator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->collator = new Collator($this->translator->getLocale());
    }

    /**
     * Cette méthode prend en argument un tableau par référence. Le tableau posséde une liste de clé de traduction.
     * Elles sont stockées dans l'index "label". A partir du domaine, la méthode va traduire les clés dans la locale
     * choisie par l'utilisateur, puis elle va appliquer un tri alphabétique sur les labels.
     * Attention : la traduction est effectuée uniquement pour pouvoir trier. Elle n'est pas appliquée au tableau.
     *
     * @param &array $listeStrings Référence vers un tableau contenant une liste de clés de traduction
     * @param string $domaine Nom du domaine qui contient les clés de traduction
     */
    public function trierListeStringsSelonLocale(&$listeStrings, $domaine)
    {
        $collator = $this->collator;

        usort(
            $listeStrings,
            function ($a, $b) use ($collator, $domaine) {
                return $collator->compare(
                    $this->translator->trans($a->label, array(), $domaine),
                    $this->translator->trans($b->label, array(), $domaine)
                );
            }
        );
    }
}
