<?php

namespace Anh\FeedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class FeedController extends Controller
{
    public function indexAction(Request $request, $feed, $parameters = array())
    {
        $parameters['modifiedSince'] = $this->getModifiedSince($request);

        $resolver = $this->get('anh_feed.resolver');
        $provider = $resolver->getProvider($feed);

        if ($parameters['modifiedSince'] >= $provider->getUpdatedAt($parameters)) {
            $response = new Response();
            $response->setNotModified();

            return $response;
        }

        $result = $resolver->resolve($feed, $parameters);

        $response = new Response($result['feed']);
        $response->headers->set('Content-Type', $result['mime']);
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setLastModified($result['updatedAt']);

        return $response;
    }

    protected function getModifiedSince(Request $request)
    {
        $modifiedSince = new DateTime('1970-01-01');

        if ($request->headers->has('If-Modified-Since')) {
            $modifiedSince = DateTime::createFromString(
                $request->headers->get('If-Modified-Since')
            );
        }

        return $modifiedSince;
    }
}
