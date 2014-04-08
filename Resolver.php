<?php

namespace Anh\FeedBundle;

use Anh\FeedBuilder\FeedBuilder;

class Resolver
{
    protected $providers = array();

    public function addProvider($feed, DataProviderInterface $provider)
    {
        $this->providers[$feed] = $provider;
    }

    public function getProvider($feed)
    {
        if (!isset($this->providers[$feed])) {
            throw new \InvalidArgumentException(
                sprintf("Unable to get provider for feed '%s'.", $feed)
            );
        }

        return $this->providers[$feed];
    }

    public function getProviders()
    {
        return $this->providers;
    }

    public function resolve($name, $parameters = array(), $format = false, $validate = false)
    {
        $provider = $this->getProvider($name);

        $feed = new FeedBuilder();

        $feed = $feed
            ->setType($provider->getType())
            ->fromArray($provider->getData($name, $parameters))
        ;

        $result = array(
            'feed' => $feed->render($format),
            'type' => $feed->getType(),
            'mime' => $feed->getMimeType(),
            'updatedAt' => $provider->getUpdatedAt($parameters)
        );

        if ($validate) {
            $result['errors'] = $feed->validate();
        }

        return $result;
    }
}
