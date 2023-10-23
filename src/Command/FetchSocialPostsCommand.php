<?php

namespace SocialDataBundle\Command;

use SocialDataBundle\Processor\SocialPostBuilderProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchSocialPostsCommand extends Command
{
    protected static $defaultName = 'social-data:fetch:social-posts';
    protected static $defaultDescription = 'Fetch Social Posts';

    protected SocialPostBuilderProcessor $socialPostBuilderProcessor;

    public function __construct(SocialPostBuilderProcessor $socialPostBuilderProcessor)
    {
        $this->socialPostBuilderProcessor = $socialPostBuilderProcessor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('wallId', 'w', InputOption::VALUE_REQUIRED, 'Only perform on specific wall')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Update posts even if they\'re imported already');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->socialPostBuilderProcessor->process($input->getOption('force'), $input->getOption('wallId'));

        return 0;
    }
}
