<?php

namespace Anh\FeedBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * CLI command for rendering feeds
 */
class DumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('anh:feed:dump')
            ->setDescription('Dump feed')
            ->addArgument('feed', InputArgument::REQUIRED, 'Feed name')
            ->addArgument('parameters', InputArgument::OPTIONAL, 'Feed parameters in json format')
            ->addOption('feed-only', null, InputOption::VALUE_NONE, 'Dump only feed.')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'Set hostname.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '-1');

        $container = $this->getContainer();

        $feed = $input->getArgument('feed');

        $resolver = $container->get('anh_feed.resolver');

        if ($input->getOption('host')) {
            $host = $input->getOption('host');

            if (strpos($host, '://') === false) {
                $host = 'http://' . $host;
            }

            list($scheme, $host) = explode('://', $host);

            $host = trim($host, '/');

            $router = $container->get('router');
            $router->getContext()->setHost($host);
            $router->getContext()->setScheme($scheme);
        }

        $format = !$input->getOption('feed-only');

        $parameters = (array) json_decode($input->getArgument('parameters'), true);

        $result = $resolver->resolve($feed, $parameters, $format, true);

        if ($format) {
            $result['feed'] = preg_replace_callback('/(<\w+)(\s.+?)(\/?>)/', function($matches) {
                $body = preg_replace('/(".*?")/', '<fg=yellow>$1</fg=yellow>', $matches[2]);

                return sprintf(
                    '<fg=cyan>%s</fg=cyan><fg=blue>%s</fg=blue><fg=cyan>%s</fg=cyan>',
                    $matches[1],
                    $body,
                    $matches[3]
                );
            }, $result['feed']);

            $result['feed'] = preg_replace('/(<\/?\w+>)/', '<fg=cyan>$1</fg=cyan>', $result['feed']);
        }

        $output->writeln($result['feed']);

        if (!$input->getOption('host')) {
            $output->writeln("\n<error>Use --host option to set proper hostname instead of 'localhost'.</error>\n");
        }

        if ($input->getOption('feed-only')) {
            return;
        }

        if ($result['errors']) {
            $output->writeln('<info>Errors</info>');
            var_dump($result['errors']);
        }

        $urlGenerator = $container->get('anh_feed.url_generator');

        $template = '<info>%-12s</info>%s';
        $output->writeln(sprintf($template, 'Feed', $feed));
        $output->writeln(sprintf($template, 'Type', $result['type']));
        $output->writeln(sprintf($template, 'Mime', $result['mime']));
        $output->writeln(sprintf($template, 'Url', $urlGenerator->generate($feed, $parameters, false)));
        $output->writeln(sprintf($template, 'Errors', count($result['errors'])));
        $output->writeln(sprintf($template, 'Updated at', $result['updatedAt']->format(\DateTime::ATOM)));
    }
}
