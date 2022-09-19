<?php

namespace App\Filtres;

class DateFiltre
{

    public function trierSortiesParDate(array $listeSorties, string $dateMin, string $dateMax): array
    {
        $nouvelleListe = [];
        if ($dateMin !== '' && $dateMax !== '') {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() >= date_timestamp_set(new \DateTime(), strtotime($dateMin))
                    && $sortie->getDateHeureDebut() <= date_timestamp_set(new \DateTime(), strtotime($dateMax))) {
                    $nouvelleListe[] = $sortie;
                }
            }
        } elseif ($dateMin !== '') {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() >= date_timestamp_set(new \DateTime(), strtotime($dateMin))) {
                    $nouvelleListe[] = $sortie;
                }
            }
        } elseif ($dateMax !== '') {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() <= date_timestamp_set(new \DateTime(), strtotime($dateMax))) {
                    $nouvelleListe[] = $sortie;
                }
            }
        } else {
            $nouvelleListe = $listeSorties;
        }
        return $nouvelleListe;
    }
}