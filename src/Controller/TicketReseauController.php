<?php

namespace App\Controller;

use App\Entity\TicketNoIncident;
use App\Entity\TicketReseau;
use App\Form\ArsInstallFormType;
use App\Form\ArsRecupFormType;
use App\Form\CommentFormType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Form\TicketNoIncidentFormType;
use App\Repository\TicketNoIncidentRepository;
use App\Repository\TicketReseauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reseau")
 */
class TicketReseauController extends AbstractController
{
    #[Route('/reseau/mise-a-jour', name:'reseau_upload', methods:['GET','POST'])]
    /**
     * @Route("/gestion-tickets/no-incident/recuperate/{id}", name="no_incident_recuperate", methods={"GET","POST"})
     */
    public function index(Request $request, TicketReseauRepository $ticketReseauRepository): Response
    {
        $lastUpdatedDate = $ticketReseauRepository->findOneByLastUpdatedDate();
        //dd($lastUpdatedDate[0]['maxDate']);
        $ticketNoIncident = new TicketNoIncident();
        $form = $this->createForm(TicketNoIncidentFormType::class, $ticketNoIncident);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticketNoIncident->setEtat('traité');
            // persist object
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticketNoIncident);
            $entityManager->flush();
            // generate a success message
            $this->addFlash('success','Le magasin ' . $ticketNoIncident->getCodeMagasin() . ' a bien été enregistré' );
            // redirect to current page
            return $this->redirectToRoute('reseau_upload');
        }

        //dd($lastUpdatedDate);
        return $this->render('ticket_reseau/upload.html.twig',[
            'lastUpdatedDate' => $lastUpdatedDate,
            'noIncidentForm' => $form->createView()
        ]);
    }

    // manage all ticket
    #[Route('/reseau/gestion-tickets', name: 'reseau_manage', methods:'GET')]
    public function manage(TicketReseauRepository $ticketReseauRepository, TicketNoIncidentRepository $ticketNoIncidentRepository): Response
    {
        $openedTickets = $ticketReseauRepository->findByTicketState('Ticket_ouvert');
        $treatedTickets = $ticketReseauRepository->findByTicketState('Ticket_traité');
        $closedTickets = $ticketReseauRepository->findByTicketState('Ticket_a_fermer');

        $noIncidentTickets = $ticketNoIncidentRepository->findByTicketState('traité');
                
        $recurringStores = $ticketReseauRepository->findRecurringStores();

        return $this->render('ticket_reseau/manage.html.twig', [
            'openedTickets' => $openedTickets,
            'treatedTickets' => $treatedTickets,
            'closedTickets' => $closedTickets,
            'noIncidentTickets' => $noIncidentTickets,
            'recurringStores' => $recurringStores,
        ]);
    }

    // get details of one ticket and add comment if necessary
    #[Route('/reseau/gestion-tickets/{id}', name: 'reseau_details', methods:'GET')]
    public function details(TicketReseau $ticketReseau): Response
    {   
        if (!$ticketReseau) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketReseau->getNTicket()
            );
        } 

        return $this->render('ticket_reseau/details.html.twig', [
            'ticketReseau' => $ticketReseau,
        ]);
    }

    // comment ticket
    #[Route('/reseau/gestion-tickets/comment/{id}', name: 'reseau_comment', methods:['GET','POST'])]
    public function comment(Request $request, TicketReseau $ticketReseau): Response
    {
        if (!$ticketReseau) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketReseau->getNTicket()
            );
        } 

        $form = $this->createForm(CommentFormType::class, $ticketReseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticketReseau->setDateMaj(new \DateTime());

            $this->getDoctrine()->getManager()->flush();
            // generate a success message
            $this->addFlash('success','Le commentaire pour le ticket n° ' . $ticketReseau->getNTicket() . ' a été enregistré');
            // redirect to current page
            return $this->redirectToRoute('reseau_details', ['id' => $ticketReseau->getNTicket()]);
        }

        return $this->render('ticket_reseau/comment.html.twig', [
            'ticketReseau' => $ticketReseau,
            'commentForm' => $form->createView()
        ]);
    }

    // install ARS
    #[Route('/reseau/gestion-tickets/install/{id}', name: 'reseau_install', methods:['GET','POST'])]
    public function install(Request $request, TicketReseau $ticketReseau): Response
    {
        if (!$ticketReseau) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketReseau->getNTicket()
            );
        } 

        $form = $this->createForm(ArsInstallFormType::class, $ticketReseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $ticketReseau->setDateInstall(new \DateTime());
            $ticketReseau->setDateMaj(new \DateTime());
            $ticketReseau->setEtatTicket('Ticket_traité');

            $this->getDoctrine()->getManager()->flush();
            // generate a success message
            $this->addFlash('success','Le Ticket ' . $ticketReseau->getNTicket() . ' est traité : Une demande d\'installation d\'un kit 4G a été effectuée');
            // redirect to current page
            return $this->redirectToRoute('reseau_manage');
        }

        return $this->render('ticket_reseau/install.html.twig', [
            'ticketReseau' => $ticketReseau,
            'installForm' => $form->createView()
        ]);
    }

    // recuperate ARS
    #[Route('/reseau/gestion-tickets/recuperate/{id}', name: 'reseau_recuperate', methods:['GET','POST'])]
    public function recuperate(Request $request, TicketReseau $ticketReseau): Response
    {
        if (!$ticketReseau) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketReseau->getNTicket()
            );
        } 

        $form = $this->createForm(ArsRecupFormType::class, $ticketReseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $ticketReseau->setDateArchive(new \DateTime());
            $ticketReseau->setDateMaj(new \DateTime());
            $ticketReseau->setEtatTicket('Ticket_archivé');

            $this->getDoctrine()->getManager()->flush();
            // generate a success message
            $this->addFlash('success','Le Ticket ' . $ticketReseau->getNTicket() . ' est traité : Une demande de récupération du kit 4G a été effectuée');
            // redirect to current page
            return $this->redirectToRoute('reseau_manage');
        }

        return $this->render('ticket_reseau/recuperate.html.twig', [
            'ticketReseau' => $ticketReseau,
            'recuperateForm' => $form->createView()
        ]);
    }

    // get all recurring store details
    #[Route('/reseau/gestion-tickets/recurring/{codeMagasin}', name: 'reseau_recurring_details', methods:'GET')]
    public function recurringStoreDetails(string $codeMagasin, TicketReseauRepository $ticketReseauRepository): Response
    {
        if (!$codeMagasin) {
            throw $this->createNotFoundException(
                'No ticket found for code magasin '.$codeMagasin
            );
        } 
        
        $ticketReseau = $ticketReseauRepository->findOneBy(['codeMagasin' => $codeMagasin]);
        $recurringStoreDetails = $ticketReseauRepository->findRecurringStoreDetailsBy($ticketReseau->getCodeMagasin());
        
        return $this->render('ticket_reseau/recurring.html.twig', [
            'ticketReseau' => $ticketReseau,
            'recurringStoreDetails' => $recurringStoreDetails,
        ]);
    }
}
