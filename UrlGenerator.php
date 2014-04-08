<?php

namespace Anh\FeedBundle;

use Symfony\Component\Routing\RouterInterface;
use Anh\FeedBundle\Resolver;

class UrlGenerator
{
    protected $router;

    protected $resolver;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getRouteName($feed)
    {
        $routeName = sprintf('anh_feed_%s', $feed);
        $route = $this->router->getRouteCollection()->get($routeName);

        return $route ? $routeName : 'anh_feed_index';
    }

    public function getRoute($feed)
    {
        return $this->router->getRouteCollection()->get(
            $this->getRouteName($feed)
        );
    }

    public function generate($feed, $parameters = array(), $absolute = true)
    {
        $routeName = $this->getRouteName($feed);

        return $this->router->generate(
            $routeName,
            $this->prepareParameters(
                $routeName,
                array_merge(array('feed' => $feed), $parameters)
            ),
            $absolute
        );
    }

    /**
     * Prepares route parameters leaving only required
     *
     * @param string $routeName
     * @param array $parameters
     *
     * @return array
     */
    protected function prepareParameters($routeName, array $parameters)
    {
        $route = $this->router->getRouteCollection()->get($routeName);

        if (!$route) {
            throw new \InvalidArgumentException(
                sprintf("Unable to find route '%s'.", $routeName)
            );
        }

        return array_intersect_key(
            $parameters,
            $route->getRequirements()
        );
    }
}
