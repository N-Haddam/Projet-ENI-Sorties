<?php
namespace App\EventListener;

use App\Entity\Sortie;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;

class Archivage {
    private const DAYS_BEFORE_REMOVAL = 30;

    /**
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args): void{
        {
            // the listener methods receive an argument which gives you access to
            // both the entity object of the event and the entity manager itself
            $sortie = $args->getObject();

            // if this listener only applies to certain entity types,
            // add some code to check the entity type as early as possible
            if($sortie instanceof Sortie) {
                $dateDebut = $sortie->getDateHeureDebut();
                if($dateDebut > new \DateTimeImmutable(-self::DAYS_BEFORE_REMOVAL.' days')){
                    $entityManager = $args->getObjectManager();
                    $entityManager->remove($sortie);
                }
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void{
        {
            // the listener methods receive an argument which gives you access to
            // both the entity object of the event and the entity manager itself
            $sortie = $args->getObject();

            // if this listener only applies to certain entity types,
            // add some code to check the entity type as early as possible
            if($sortie instanceof Sortie) {
                $nombreInscript = $sortie->getParticipants()->count();
                $nombreMawParticpants = $sortie->getNbInscriptionMax();
                if($nombreInscript >= $nombreMawParticpants){
                    $sortie->setEtat();
                }
            }
        }
    }
}
