<?php

namespace App\Filtres;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

class CheckBoxFiltre
{

    /**
     * @param array $listeSorties
     * @param UserInterface|null $user
     * @param array $parametrageTwig
     * @param DefaultCheckBoxFiltre $defaultCheckBoxFiltre
     * @return array
     */
    public function triCk(array $listeSorties, ?UserInterface $user, array $parametrageTwig, DefaultCheckBoxFiltre $defaultCheckBoxFiltre): array
    {
        $listeSortiesTriCkBox = [];
        if (isset($_POST['organisateurTrue'])) {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getOrganisateur()->getUserIdentifier() === $user->getUserIdentifier()) {
                    $listeSortiesTriCkBox[] = $sortie;
                }
            }
            $parametrageTwig['ck']['organisateurTrue'] = true;
        }
        if (isset($_POST['inscritTrue'])) {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getParticipants()) {
                    foreach ($sortie->getParticipants() as $participant) {
                        if (($participant->getUserIdentifier() === $user->getUserIdentifier())
                            && !in_array($sortie, $listeSortiesTriCkBox)) {
                            $listeSortiesTriCkBox[] = $sortie;
                        }
                    }
                }
            }
            $parametrageTwig['ck']['inscritTrue'] = true;
        }
        if (isset($_POST['inscritFalse'])) {
            foreach ($listeSorties as $sortie) {
                if (!$sortie->getParticipants()) { // TODO modifier Sortie pour que participants soit not null
                    $listeSortiesTriCkBox[] = $sortie;
                } else {
                    $test = false;
                    foreach ($sortie->getParticipants() as $participant) {
                        if ($participant->getUserIdentifier() === $user->getUserIdentifier()) {
                            $test = true;
                            break;
                        }
                    }
                    if (!$test && !in_array($sortie, $listeSortiesTriCkBox)) {
                        $listeSortiesTriCkBox[] = $sortie;
                    }
                }
            }
            $parametrageTwig['ck']['inscritFalse'] = true;
        }
        if (isset($_POST['sortiesPassees'])) {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() <= new DateTime() && !in_array($sortie, $listeSortiesTriCkBox)) {
                    $listeSortiesTriCkBox[] = $sortie;
                }
            }
            $parametrageTwig['ck']['sortiesPassees'] = true;
        } else {
            $listeSortiesTriCkBox = $defaultCheckBoxFiltre->triCkbDefault($listeSortiesTriCkBox);
        }
        $listeSorties = $listeSortiesTriCkBox;
        return array($parametrageTwig, $listeSorties);
    }
}