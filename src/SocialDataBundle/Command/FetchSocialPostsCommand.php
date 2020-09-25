<?php

namespace SocialDataBundle\Command;

use SocialDataBundle\Processor\SocialPostBuilderProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchSocialPostsCommand extends Command
{
    /**
     * @var SocialPostBuilderProcessor
     */
    protected $socialPostBuilderProcessor;

    /**
     * @param SocialPostBuilderProcessor $socialPostBuilderProcessor
     */
    public function __construct(SocialPostBuilderProcessor $socialPostBuilderProcessor)
    {
        $this->socialPostBuilderProcessor = $socialPostBuilderProcessor;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('social-data:fetch:social-posts')
            ->setDescription('Fetch Social Posts');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->socialPostBuilderProcessor->process();

        return 0;
    }
}
