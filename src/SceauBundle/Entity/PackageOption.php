<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PackageOption
 *
 * @ORM\Table(name="PackageOption")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\PackageOptionRepository")
 */
class PackageOption
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Option
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Option", inversedBy="packageOptions")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id", nullable=false)
     */
    private $option;

    /**
     * @var Package
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Package", inversedBy="packageOptions")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id", nullable=false)
     */
    private $package;
    
    /**
     * @var OptionType
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\OptionType")
     * @ORM\JoinColumn(name="optiontype_id", referencedColumnName="id", nullable=false)
     **/
    private $optionType;


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
     * Set option
     *
     * @param Option $option
     * @return PackageOption
     */
    public function setOption(Option $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set package
     *
     * @param Package $package
     * @return PackageOption
     */
    public function setPackage(Package $package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set optionType
     *
     * @param OptionType $optionType
     * @return PackageOption
     */
    public function setOptionType(OptionType $optionType)
    {
        $this->optionType = $optionType;

        return $this;
    }

    /**
     * Get optionType
     *
     * @return OptionType
     */
    public function getOptionType()
    {
        return $this->optionType;
    }
}
