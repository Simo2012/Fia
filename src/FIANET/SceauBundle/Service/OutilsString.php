<?php

namespace FIANET\SceauBundle\Service;

use Collator;

/**
 * Service contenant des outils qui permettent de manipuler des chaines de caractères.
 */
class OutilsString
{
    private $translator;
    private $collator;

    public function __construct($translator)
    {
        $this->translator = $translator;
        $this->collator = new Collator($this->translator->getLocale());
    }

    /**
     * Cette méthode prend un tableau par référence. Il contient une liste de clé de traduction. A partir du domaine,
     * elle va traduire les clés dans la locale choisie par l'utilisateur. Puis elle va appliquer un tri alphabétique.
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
