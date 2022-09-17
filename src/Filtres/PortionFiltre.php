<?php

namespace App\Filtres;

class PortionFiltre
{

    public function trierSortiesParPortion(array $listeSorties, string $portion): array
    {
        $nouvelleListe = [];
        foreach ($listeSorties as $sortie) {
            if (str_contains(strtolower($sortie->getNom()), strtolower($portion))) {
                $nouvelleListe[] = $sortie;
            }
        }
        return $nouvelleListe;
    }
}