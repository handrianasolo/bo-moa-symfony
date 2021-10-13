<?php

namespace App\Controller;

use App\Repository\TicketIbmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ibm")
 */
class TicketIbmController extends AbstractController
{
    /**
     * upload ibm excel file
     * @Route("/mise-a-jour", name="ibm_upload", methods={"GET"})
     */
    public function index(TicketIbmRepository $ticketIbmRepository): Response
    {
        $lastUpdatedDate = $ticketIbmRepository->findOneByLastUpdatedDate();
        //dd($lastUpdatedDate[0]['maxDate']);
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

        return $this->render('ticket_ibm/upload.html.twig', [
            'lastUpdatedDate' => $lastUpdatedDate,
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
     * @Route("/mise-a-jour/details/{id}-days", name="ibm_details_days", methods={"GET"})
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
     * @Route("/mise-a-jour/details/between-{id1}-and-{id2}-days", name="ibm_details_weeks", methods={"GET"})
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
     * @Route("/mise-a-jour/details/more-than-{id}-days", name="ibm_details_months", methods={"GET"})
     */
    public function detailsMoreOneMonth(int $id, TicketIbmRepository $ticketIbmRepository): Response
    {
        $detailsByMoreOneMonth = $ticketIbmRepository->findByMoreOneMonth($id);

        return $this->render('ticket_ibm/details-months.html.twig', [
            'detailsByMoreOneMonth' => $detailsByMoreOneMonth,
        ]);
    }

    /**
     * get all recurring tickets
     * @Route("/gestion-tickets", name="ibm_manage", methods={"GET"})
     */
    public function manage(TicketIbmRepository $ticketIbmRepository): Response
    {
        $recurringTickets = $ticketIbmRepository->findRecurringTickets('NON_RESOLU');

        return $this->render('ticket_ibm/manage.html.twig', [
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
