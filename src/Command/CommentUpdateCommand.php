<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommentUpdateCommand extends Command
{
    private $sortieRepository;

    protected static $defaultName = 'app:trip:update';

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Update school field trips in the database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

        } else {

        $this->sortieRepository->sortiesACloturer();
        $this->sortieRepository->sortiesPasse();
        $this->sortieRepository->sortiesEnCours();
        $io->success(sprintf('Field trips updated'));

        }
        return 0;
    }
}
