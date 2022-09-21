<?php

namespace App\Command;


use App\Entity\Campus;
use App\Entity\Participant;


use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function PHPUnit\Framework\throwException;


class ImportCommand extends Command
{
    protected static $defaultName = 'app:users:update';
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct($projectDir, EntityManagerInterface $entityManager,
                                UserPasswordHasherInterface $passwordEncoder,
    )
    {
        //j'ai bindé dans services.yml pour recupérer ici le directory du projet dans $projectDir
        $this->projectDir=$projectDir;
        $this->entityManager=$entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function configure()
    {
        // Name and description for app/console command
        $this->setDescription('Import users from CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Convert CSV file into iterable php
        $participants = $this->getCsvRowsAsArrays();

        /** @var ParticipantRepository $participantsRepo */ //Pour l'autocomplétion
        $participantsRepo = $this->entityManager->getRepository(Participant::class);


        foreach ($participants as $participant)
        {
            $participantExiste = $participantsRepo->findOneBy(['email'=>$participant['email']]);

            if($participantExiste){

                $io = new SymfonyStyle($input, $output);

                $io->error(sprintf("User email %s already taken", $participantExiste->getEmail()));
            }else {

            $campus = $this->entityManager->getRepository(Campus::class)
                ->findOneBy(['nom' => $participant['campus']]);
            $nouveauParticipant = new Participant();
            $nouveauParticipant->setEmail($participant['email']);
            $nouveauParticipant->setPrenom($participant['prenom']);
            $nouveauParticipant->setNom($participant['nom']);
            $nouveauParticipant->setActif($participant['actif']);
            $hashedPassword = $this->passwordEncoder->hashPassword($nouveauParticipant, $participant['password']);
            $nouveauParticipant->setPassword($hashedPassword);
            $nouveauParticipant->setRoles([$participant['roles']]);
            $nouveauParticipant->setAdministrateur($participant['administrateur']);
            $nouveauParticipant->setCampus($campus);
            $nouveauParticipant->setPictureFileName($participant['pictureFileName']);
            $nouveauParticipant->setPseudo($participant['pseudo']);
            $nouveauParticipant->setTelephone($participant['telephone']);

            $participantsRepo->add($nouveauParticipant, true);

            //create new record if matching db
            $io = new SymfonyStyle($input, $output);

            $io->success(sprintf('Inscription of %s %s worked', $participant['prenom'],$participant['nom']));


            }
        }

        return Command::SUCCESS;

    }

    public function getCsvRowsAsArrays()
    {
    $inputFile = $this->projectDir.'/public/files/users.csv';

    $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

    return $decoder->decode(file_get_contents($inputFile), 'csv');
    }
}
