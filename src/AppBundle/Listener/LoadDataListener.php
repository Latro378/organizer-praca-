<?php

namespace AppBundle\Listener;

use AncaRebeca\FullCalendarBundle\Event\CalendarEvent;
use AncaRebeca\FullCalendarBundle\Model\Event;
use AncaRebeca\FullCalendarBundle\Model\FullCalendarEvent;
use AppBundle\Entity\CalendarEvent as MyCustomEvent;
use AppBundle\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;

class LoadDataListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * LoadDataListener constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

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
        $result = $this->em->getRepository(Todo::class)->findAll();
        foreach ($result as $item) {
            $calendarEvent->addEvent(new Event($item->getName(), $item->getDate()));
        }
    }
}