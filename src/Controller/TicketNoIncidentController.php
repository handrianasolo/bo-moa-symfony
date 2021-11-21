<?php

namespace App\Controller;

use App\Entity\TicketNoIncident;
use App\Form\ArsRecupNoIncidentFormType;
use App\Form\CommentNoIncidentFormType;
use App\Form\TicketNoIncidentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reseau")
 */
class TicketNoIncidentController extends AbstractController
{

    /**
     * @Route("/gestion-tickets/no-incident/{id}", name="no_incident_details", methods={"GET"})
     */
    public function details(TicketNoIncident $ticketNoIncident): Response
    {   
        if (!$ticketNoIncident) {
            throw $this->createNotFoundException();
        } 

        return $this->render('ticket_no_incident/details.html.twig', [
            'ticketNoIncident' => $ticketNoIncident,
        ]);
    }

    /**
     * @Route("/gestion-tickets/no-incident/comment/{id}", name="no_incident_comment", methods={"GET","POST"})
     */
    public function comment(Request $request, TicketNoIncident $ticketNoIncident): Response
    {   
        if (!$ticketNoIncident) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketNoIncident->getId()
            );
        } 

        $form = $this->createForm(CommentNoIncidentFormType::class, $ticketNoIncident);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticketNoIncident->setDateMaj(new \DateTime());

            $this->getDoctrine()->getManager()->flush();
            // generate a success message
            $this->addFlash('success','Le commentaire pour le magasin ' . $ticketNoIncident->getCodeMagasin() . ' a bien été enregistré');
            // redirect to current page
            return $this->redirectToRoute('no_incident_details', ['id' => $ticketNoIncident->getId()]);
        }

        return $this->render('ticket_no_incident/comment.html.twig', [
            'ticketNoIncident' => $ticketNoIncident,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/gestion-tickets/no-incident/recuperate/{id}", name="no_incident_recuperate", methods={"GET","POST"})
     */
    public function recuperate(Request $request, TicketNoIncident $ticketNoIncident): Response
    {
        if (!$ticketNoIncident) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$ticketNoIncident->getId()
            );
        } 
        
        $form = $this->createForm(ArsRecupNoIncidentFormType::class, $ticketNoIncident);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ticketNoIncident->setDateArchive(new \DateTime());
            $ticketNoIncident->setDateMaj(new \DateTime());
            $ticketNoIncident->setEtat('archivé');
            
            $this->getDoctrine()->getManager()->flush();
            // generate a success message
            $this->addFlash('success','Une demande de récupération du kit 4G a été effectuée pour le magasin ' . $ticketNoIncident->getCodeMagasin());
            // redirect to current page
            return $this->redirectToRoute('reseau_manage');
        }

        return $this->render('ticket_no_incident/recuperate.html.twig', [
            'ticketNoIncident' => $ticketNoIncident,
            'recuperateForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajouter-ticket-no-incident", name="reseau_add", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $ticketNoIncident = new TicketNoIncident();
        $form = $this->createForm(TicketNoIncidentFormType::class, $ticketNoIncident);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $ticketNoIncident->setEtat('traité');
            $ticketNoIncident->setDateMaj(new \DateTime());
            // persist object
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticketNoIncident);
            $entityManager->flush();
            // generate a success message
            $this->addFlash('success','Le magasin ' . $ticketNoIncident->getCodeMagasin() . ' a bien été enregistré.' );
            // redirect to current page
            return $this->redirectToRoute('reseau_add');
        }

        return $this->render('ticket_no_incident/create.html.twig',[
            'noIncidentForm' => $form->createView(),
        ]);
    }
}
