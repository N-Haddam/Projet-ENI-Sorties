<?php
//namespace App\EventListener;
//
//use App\Entity\Sortie;
//use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
//use Doctrine\ORM\Events;
//use Doctrine\Persistence\Event\LifecycleEventArgs;
//
//class DatabaseActivitySubscriber implements EventSubscriberInterface
//{
//    private const DAYS_BEFORE_REMOVAL = 30;
//    // this method can only return the event names; you cannot define a
//    // custom method name to execute when each event triggers
//
//    public function getSubscribedEvents(): array
//    {
//        return [
//            Events::postPersist,
//            Events::postRemove,
//            Events::postUpdate,
//        ];
//    }
//
//    // callback methods must be called exactly like the events they listen to;
//    // they receive an argument of type LifecycleEventArgs, which gives you access
//    // to both the entity object of the event and the entity manager itself
//    public function postPersist(LifecycleEventArgs $args): void
//    {
//        $sortie = $args->getObject();
//        if($sortie instanceof Sortie) {
//            $dateDebut = $sortie->getDateHeureDebut();
//            $nbParticipants = $sortie->getParticipants();
//            $nbInscriptionMax = $sortie->getNbInscriptionMax();
//            if ($dateDebut < new \DateTimeImmutable(-self::DAYS_BEFORE_REMOVAL . ' days')) {
//                $entityManager = $args->getObjectManager();
//                $entityManager->remove($sortie);
//            }
//        }
//        $this->logActivity('persist', $args);
//    }
//
//    public function postRemove(LifecycleEventArgs $args): void
//    {
//        $this->logActivity('remove', $args);
//    }
//
//    public function postUpdate(LifecycleEventArgs $args): void
//    {
//        $this->logActivity('update', $args);
//    }
//
//    private function logActivity(string $action, LifecycleEventArgs $args): void
//    {
//        $entity = $args->getObject();
//
//        // if this subscriber only applies to certain entity types,
//        // add some code to check the entity type as early as possible
//        if (!$entity instanceof Sortie) {
//            return;
//        }
//
//        // ... get the entity information and log it somehow
//    }
//}
//
