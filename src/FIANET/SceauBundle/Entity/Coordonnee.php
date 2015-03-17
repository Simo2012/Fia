<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coordonnee
 *
 * @ORM\Table(name="Coordonnee")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\CoordonneeRepository")
 */
class Coordonnee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="compAdresse", type="string", length=255, nullable=true)
     */
    private $compAdresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codePostal", type="string", length=20)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=50)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="telephoneFixe", type="string", length=20, nullable=true)
     */
    private $telephoneFixe;

    /**
     * @var string
     *
     * @ORM\Column(name="telephoneMobile", type="string", length=20, nullable=true)
     */
    private $telephoneMobile;


    /**
     * @var Pays
     *
     * @ORM\ManyToOne(targetEntity="FIANET\SceauBundle\Entity\Pays")
     * @ORM\JoinColumn(name="pays_id", referencedColumnName="id", nullable=false)
     **/
    private $pays;


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
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Coordonnee
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set compAdresse
     *
     * @param string $compAdresse
     *
     * @return Coordonnee
     */
    public function setCompAdresse($compAdresse)
    {
        $this->compAdresse = $compAdresse;

        return $this;
    }

    /**
     * Get compAdresse
     *
     * @return string
     */
    public function getCompAdresse()
    {
        return $this->compAdresse;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Coordonnee
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Coordonnee
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set telephoneFixe
     *
     * @param string $telephoneFixe
     *
     * @return Coordonnee
     */
    public function setTelephoneFixe($telephoneFixe)
    {
        $this->telephoneFixe = $telephoneFixe;

        return $this;
    }

    /**
     * Get telephoneFixe
     *
     * @return string
     */
    public function getTelephoneFixe()
    {
        return $this->telephoneFixe;
    }

    /**
     * Set telephoneMobile
     *
     * @param string $telephoneMobile
     *
     * @return Coordonnee
     */
    public function setTelephoneMobile($telephoneMobile)
    {
        $this->telephoneMobile = $telephoneMobile;

        return $this;
    }

    /**
     * Get telephoneMobile
     *
     * @return string
     */
    public function getTelephoneMobile()
    {
        return $this->telephoneMobile;
    }

    /**
     * Set pays
     *
     * @param Pays $pays
     *
     * @return Coordonnee
     */
    public function setPays(Pays $pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return Pays
     */
    public function getPays()
    {
        return $this->pays;
    }
}
