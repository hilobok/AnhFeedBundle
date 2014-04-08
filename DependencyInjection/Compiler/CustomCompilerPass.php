<?php

namespace Anh\FeedBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CustomCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $resolver = $container->getDefinition('anh_feed.resolver');

        $taggedServices = $container->findTaggedServiceIds('anh_feed.data_provider');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $resolver->addMethodCall(
                    'addProvider',
                    array($attributes['feed'], new Reference($id))
                );
            }
        }
    }
}
