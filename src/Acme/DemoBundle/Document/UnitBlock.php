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
     * @PHPCR\String
     */
    protected $text = 'Read more!';

    /**
     * @PHPCR\String(nullable=true)
     */
    protected $url;

    /**
     * @PHPCR\String(nullable=true)
     */
    protected $route;

    /**
     * @PHPCR\String
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
