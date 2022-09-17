<?php

namespace App\Filtres;

class DefaultCheckBoxFiltre
{

    /**
     * @param array $listeSorties
     * @return array
     */
    public function triCkbDefault(array $listeSorties): array
    {
        $listeTmp = [];
        foreach ($listeSorties as $sortie) {
            if ($sortie->getDateHeureDebut() >= new \DateTime()) {
                $listeTmp[] = $sortie;
            }
        }
        $listeSorties = $listeTmp;
        return $listeSorties;
    }
}