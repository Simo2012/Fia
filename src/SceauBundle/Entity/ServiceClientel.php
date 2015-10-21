<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceClientel
 *
 * @ORM\Table(name="ServiceClientel")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\ServiceClientelRepository")
 */
class ServiceClientel
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
     * @ORM\Column(name="tel", type="string", length=20)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="coutappel", type="string", length=20)
     */
    private $coutappel;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=20)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=20)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="ouverture", type="string", length=100)
     */
    private $ouverture;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500)
     */
    private $description;


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
     * Set tel
     *
     * @param string $tel
     *
     * @return ServiceClientel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set coutappel
     *
     * @param string $coutappel
     *
     * @return ServiceClientel
     */
    public function setCoutappel($coutappel)
    {
        $this->coutappel = $coutappel;

        return $this;
    }

    /**
     * Get coutappel
     *
     * @return string
     */
    public function getCoutappel()
    {
        return $this->coutappel;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return ServiceClientel
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ServiceClientel
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set ouverture
     *
     * @param string $ouverture
     *
     * @return ServiceClientel
     */
    public function setOuverture($ouverture)
    {
        $this->ouverture = $ouverture;

        return $this;
    }

    /**
     * Get ouverture
     *
     * @return string
     */
    public function getOuverture()
    {
        return $this->ouverture;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ServiceClientel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}

