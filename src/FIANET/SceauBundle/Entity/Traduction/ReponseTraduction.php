<?php
namespace FIANET\SceauBundle\Entity\Traduction;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Table(name="ReponseTraduction", indexes={
 *      @ORM\Index(name="reponseTraduction_idx", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class ReponseTraduction extends AbstractTranslation
{
    /**
     * All required columns are mapped through inherited superclass
     */
}
