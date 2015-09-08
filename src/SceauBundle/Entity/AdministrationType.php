<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdministrationType
 *
 * @ORM\Table(name="AdministrationType")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\AdministrationTypeRepository")
 */
class AdministrationType
{
    const LIEN_DIRECT = 1;
    const VIA_CERTISSIM = 2;
    const FLUX_XML = 3;
    const CSV_AUTO = 4;
    const CSV_MANUEL = 5;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return AdministrationType
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }
}
