<?php

namespace App\Controller;

use App\Repository\TicketIbmRepository;
use App\Repository\TicketReseauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(TicketReseauRepository $ticketReseauRepository, TicketIbmRepository $ticketIbmRepository): Response
    {
        $openedTickets = $ticketReseauRepository->findByTicketState('Ticket_ouvert');
        $treatedTickets = $ticketReseauRepository->findByTicketState('Ticket_traité');
        $closedTickets = $ticketReseauRepository->findByTicketState('Ticket_a_fermer');

        $impactHigh = $ticketIbmRepository->findByImpact('High');
        $impactMedium = $ticketIbmRepository->findByImpact('Medium');
        $impactLow = $ticketIbmRepository->findByImpact('Low');

        $urgenceHigh = $ticketIbmRepository->findByUrgence('High');
        $urgenceMedium = $ticketIbmRepository->findByUrgence('Medium');
        $urgenceLow = $ticketIbmRepository->findByUrgence('Low');

        return $this->render('home/index.html.twig', [
            'openedTickets' => $openedTickets,
            'treatedTickets' => $treatedTickets,
            'closedTickets' => $closedTickets,
            'impactHigh' => $impactHigh,
            'impactMedium' => $impactMedium,
            'impactLow' => $impactLow,
            'urgenceHigh' => $urgenceHigh,
            'urgenceMedium' => $urgenceMedium,
            'urgenceLow' => $urgenceLow,
        ]);
    }
}
