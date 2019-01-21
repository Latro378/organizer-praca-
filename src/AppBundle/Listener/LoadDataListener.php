<?php

namespace AppBundle\Listener;

use AncaRebeca\FullCalendarBundle\Event\CalendarEvent;
use AncaRebeca\FullCalendarBundle\Model\Event;
use AncaRebeca\FullCalendarBundle\Model\FullCalendarEvent;
use AppBundle\Entity\CalendarEvent as MyCustomEvent;
use AppBundle\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class LoadDataListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Security
     */
    private $security;

    /**
     * LoadDataListener constructor.
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->em = $entityManager;
        $this->security = $security;
    }

    /**
     * @param CalendarEvent $calendarEvent
     *
     * @return FullCalendarEvent[]
     */
    public function loadData(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStart();
        $endDate = $calendarEvent->getEnd();
        $filters = $calendarEvent->getFilters();
        $user = $this->security->getUser();
        $result = $this->em->getRepository(Todo::class)->findBy(['user' => $user]);
        foreach ($result as $item) {
            $event=new Event('a',$item->getDate());
            $event->setTitle($item->getName($item->getName()));
            $event->setStartDate($item->getDate());
            $event->setAllDay('false');
            $event->setEndDate($item->getDateEnd());
            $calendarEvent->addEvent($event);

        }
    }
}