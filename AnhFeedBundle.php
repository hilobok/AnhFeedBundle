<?php

namespace Anh\FeedBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Anh\FeedBundle\DependencyInjection\Compiler\CustomCompilerPass;

class AnhFeedBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CustomCompilerPass());
    }
}
