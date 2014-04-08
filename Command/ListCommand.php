<?php

namespace Anh\FeedBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * CLI command for rendering feeds
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('anh:feed:list')
            ->setDescription('List available feeds')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resolver = $this->getContainer()->get('anh_feed.resolver');
        $urlGenerator = $this->getContainer()->get('anh_feed.url_generator');

        $data = array();
        foreach ($resolver->getProviders() as $feed => $provider) {
            $route = $urlGenerator->getRoute($feed);

            if (!$route) {
                throw new \InvalidArgumentException(
                    sprintf("Unable to find route for feed '%s'.", $feed)
                );
            }

            $data[] = array(
                'feed' => $feed,
                'type' => $provider->getType(),
                'url' => $route->getPattern() ?: $route->getPath(),
                'provider' => get_class($provider)
            );
        }

        if (empty($data)) {
            $output->writeln('No feeds.');

            return;
        }

        $template = '';
        foreach (array('feed', 'type', 'url', 'provider') as $key) {
            $template .= sprintf('%%-%ds', $this->getMaxLength($key, $data) + 1);
        }

        $output->writeln(
            sprintf("<info>{$template}</info>", 'Feed', 'Type', 'Url', 'Provider')
        );

        foreach ($data as $value) {
            $output->writeln(
                sprintf(
                    $template,
                    $value['feed'],
                    $value['type'],
                    $value['url'],
                    $value['provider']
                )
            );
        }
    }

    protected function getMaxLength($key, $array)
    {
        $maxLength = 0;

        foreach ($array as $value) {
            if (strlen($value[$key]) > $maxLength) {
                $maxLength = strlen($value[$key]);
            }
        }

        return $maxLength;
    }
}
