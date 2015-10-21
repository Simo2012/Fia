<?php

namespace SceauBundle\Entity;


class ArticlePresse
{

    private $id;
    private $title;
    private $date;
    private $content;
    private $source;
    private $urlSource;
    private $published;


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
     * Set title
     *
     * @param string $title
     *
     * @return ArticlePresse
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ArticlePresse
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return ArticlePresse
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return ArticlePresse
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set urlSource
     *
     * @param string $urlSource
     *
     * @return ArticlePresse
     */
    public function setUrlSource($urlSource)
    {
        $this->urlSource = $urlSource;

        return $this;
    }

    /**
     * Get urlSource
     *
     * @return string
     */
    public function getUrlSource()
    {
        return $this->urlSource;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return ArticlePresse
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }


    /**
     * Enable to activate or deactivate an article
     *
     * @param boolean $active true to activate or false to deactivate
     * @return boolean
     */
    public function activate($active)
    {
        if ($active)
        {
            $this->setPublished(true);
            return true;
        }
        else
        {
            $this->setPublished(false);
            return false;
        }

    }
}

