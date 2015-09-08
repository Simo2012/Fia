<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="Produit")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\ProduitRepository")
 */
class Produit
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
     * @ORM\Column(name="idClient", type="string", length=50)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="codeEAN", type="string", length=13, nullable=true)
     */
    private $codeEAN;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="urlImage", type="string", length=255, nullable=true)
     */
    private $urlImage;


    /**
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Categorie")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id", nullable=false)
     */
    private $categorie;


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
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Produit
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set codeEAN
     *
     * @param string $codeEAN
     *
     * @return Produit
     */
    public function setCodeEAN($codeEAN)
    {
        $this->codeEAN = $codeEAN;

        return $this;
    }

    /**
     * Get codeEAN
     *
     * @return string
     */
    public function getCodeEAN()
    {
        return $this->codeEAN;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Produit
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

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Produit
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set urlImage
     *
     * @param string $urlImage
     *
     * @return Produit
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;

        return $this;
    }

    /**
     * Get urlImage
     *
     * @return string
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }

    /**
     * Set categorie
     *
     * @param Categorie $categorie
     * @return Produit
     */
    public function setCategorie(Categorie $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}
