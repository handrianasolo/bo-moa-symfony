<?php

namespace App\Controller;

use App\Repository\TicketIbmRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ibm")
 * 
 * @IsGranted("ROLE_ADMIN")
 */
class TicketIbmController extends AbstractController
{
    /**
     * @Route("/gestion-tickets", name="ibm_manage", methods={"GET"})
     */
    public function index(TicketIbmRepository $ticketIbmRepository): Response
    {
        $sixDays = $ticketIbmRepository->findByDays(6);
        $fiveDays = $ticketIbmRepository->findByDays(5);
        $fourDays = $ticketIbmRepository->findByDays(4);
        $threeDays = $ticketIbmRepository->findByDays(3);
        $twoDays = $ticketIbmRepository->findByDays(2);
        $yesterday = $ticketIbmRepository->findByDays(1);

        $threeWeeks = $ticketIbmRepository->findByWeeks(20,27);
        $twoWeeks = $ticketIbmRepository->findByWeeks(13,20);
        $oneWeek = $ticketIbmRepository->findByWeeks(6,13);

        $moreOneMonth = $ticketIbmRepository->findByMoreOneMonth(27);

        return $this->render('ticket_ibm/manage.html.twig', [
            'sixDays' => $sixDays,
            'fiveDays' => $fiveDays,
            'fourDays' => $fourDays,
            'threeDays' => $threeDays,
            'twoDays' => $twoDays,
            'yesterday' => $yesterday,
            'threeWeeks' => $threeWeeks,
            'twoWeeks' => $twoWeeks,
            'oneWeek' => $oneWeek,
            'moreOneMonth' => $moreOneMonth
        ]);
    }

    /**
     * get ibm details
     * @Route("/gestion-tickets/details/{id}-days", name="ibm_details_days", methods={"GET"})
     */
    public function detailsDays(int $id, TicketIbmRepository $ticketIbmRepository): Response
    {
        $detailsByDays = $ticketIbmRepository->findByDays($id);

        return $this->render('ticket_ibm/details-days.html.twig', [
            'detailsTitle' => $id,
            'detailsByDays' => $detailsByDays,
        ]);
    }

    /**
     * get ibm details
     * @Route("/gestion-tickets/details/between-{id1}-and-{id2}-days", name="ibm_details_weeks", methods={"GET"})
     */
    public function detailsWeeks(int $id1, int $id2, TicketIbmRepository $ticketIbmRepository): Response
    {
        $detailsByWeeks = $ticketIbmRepository->findByWeeks($id1,$id2);

        return $this->render('ticket_ibm/details-weeks.html.twig', [
            'detailsTitle1' => $id1,
            'detailsTitle2' => $id2,
            'detailsByWeeks' => $detailsByWeeks,
        ]);
    }

    /**
     * get ibm details
     * @Route("/gestion-tickets/details/more-than-{id}-days", name="ibm_details_months", methods={"GET"})
     */
    public function detailsMoreOneMonth(int $id, TicketIbmRepository $ticketIbmRepository): Response
    {
        $detailsByMoreOneMonth = $ticketIbmRepository->findByMoreOneMonth($id);

        return $this->render('ticket_ibm/details-months.html.twig', [
            'detailsByMoreOneMonth' => $detailsByMoreOneMonth,
        ]);
    }

    /**
     * @Route("/recherche-avance", name="ibm_search", methods={"GET"})
     */
    public function search(TicketIbmRepository $ticketIbmRepository): Response
    {
        // get all recurring NON_RESOLU tickets
        $recurringTickets = $ticketIbmRepository->findRecurringTickets('NON_RESOLU');

        return $this->render('ticket_ibm/search.html.twig', [
            'recurringTickets' => $recurringTickets,
        ]);
    }

    /**
     * get all recurring tickets details
     * @Route("/gestion-tickets/{nIncident}", name="ibm_recurring_details", methods={"GET"})
     */
    public function recurringStoreDetails(string $nIncident, TicketIbmRepository $ticketIbmRepository): Response
    {
        $recurringTicketDetails = $ticketIbmRepository->findRecurringTicketDetailsBy($nIncident);
        //dd($recurringTicketDetails);
        return $this->render('ticket_ibm/recurring.html.twig', [
            'recurringTicketDetails' => $recurringTicketDetails,
        ]);
    }
}
