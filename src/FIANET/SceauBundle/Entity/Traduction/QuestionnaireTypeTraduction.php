<?php
namespace FIANET\SceauBundle\Entity\Traduction;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Table(name="QuestionnaireTypeTraduction", indexes={
 *      @ORM\Index(name="questionnaireTypeTraduction_idx", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class QuestionnaireTypeTraduction extends AbstractTranslation
{
    /**
     * All required columns are mapped through inherited superclass
     */
}
