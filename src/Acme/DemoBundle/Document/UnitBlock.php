<?php

namespace Acme\DemoBundle\Document;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * @PHPCR\Document(referenceable=true)
 */
class UnitBlock extends AbstractBlock
{
    /**
     * @PHPCR\Field(type="string")
     */
    protected $text = 'Read more!';

    /**
     * @PHPCR\Field(type="string", nullable=true)
     */
    protected $url;

    /**
     * @PHPCR\Field(type="string", nullable=true)
     */
    protected $route;

    /**
     * @PHPCR\Field(type="string")
     */
    protected $image;

    public function getType()
    {
        return 'acme_demo.block.unit';
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setImage($url)
    {
        $this->image = $url;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getRoute()
    {
        return $this->route;
    }
}
