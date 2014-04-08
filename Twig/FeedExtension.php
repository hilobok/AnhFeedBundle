<?php

namespace Anh\FeedBundle\Twig;

use Anh\FeedBundle\UrlGenerator;

class FeedExtension extends \Twig_Extension
{
    protected $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('feedUrl', array($this, 'feedUrl')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'anh_feed_builder';
    }

    public function feedUrl($feed, $parameters = array(), $absolute = false)
    {
        return $this->urlGenerator->generate($feed, $parameters, $absolute);
    }
}