<?php
namespace App\Command;

use App\Repository\SortieRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommentCleanupCommand extends Command
{
    private $sortieRepository;

    protected static $defaultName = 'app:trip:cleanup';

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Deletes school field trips initiated > 30 days from the database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->sortieRepository->countOldRejected();
        } else {
            $count = $this->sortieRepository->deleteOldRejected();
        }

        $io->success(sprintf('Deleted "%d" old field trips', $count));

        return 0;
    }
}