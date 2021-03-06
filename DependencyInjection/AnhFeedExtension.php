<?php

namespace Anh\FeedBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AnhFeedExtension extends Extension /*implements PrependExtensionInterface*/
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

// var_dump($config['feeds']);
    }

    // public function prepend(ContainerBuilder $container)
    // {
    //     $container->prependExtensionConfig('twig', array(
    //         'form' => array(
    //             'resources' => array(
    //                 'AnhAdminBundle::fields.html.twig'
    //             )
    //         )
    //     ));

    //     $container->prependExtensionConfig('assetic', array(
    //         'bundles' => array(
    //             'AnhAdminBundle'
    //         )
    //     ));
    // }
}
