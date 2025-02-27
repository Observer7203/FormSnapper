<?php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gedmo\Timestampable\TimestampableListener;

class TimestampableSubscriber implements EventSubscriber
{
    private TimestampableListener $listener;

    public function __construct()
    {
        $this->listener = new TimestampableListener();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->listener->prePersist($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $this->listener->onFlush($em);
    }
}
