<?php

namespace App\Controller;

use App\Entity\TicketNoIncident;
use App\Entity\TicketReseau;
use App\Form\ArsInstallFormType;
use App\Form\ArsRecupFormType;
use App\Form\CommentFormType;
use App\Form\ExcelFileFormType;
use App\Form\TicketNoIncidentFormType;
use App\Repository\TicketNoIncidentRepository;
use App\Repository\TicketReseauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Include PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * @Route("/reseau")
 */
class TicketReseauController extends AbstractController
{

    private function convertFRdatetimeToUS (string $datetime): string
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("/", $dateAndTime[0]);

        return  $date[2] . "-" . $date[1] . "-" . $date[0] . " " . $dateAndTime[1]; 
    } 

    /**
     * get data in database
     */
    private function getData(TicketReseauRepository $ticketReseauRepository): array
    {
        $dbTickets = array();
        $tickets = $ticketReseauRepository->findAll();

        foreach($tickets as $ticket) {
            $list = array();
            array_push(	$list, $ticket->getEtatTicket());
            array_push(	$list, $ticket->getDateCreation());
            array_push(	$list, $ticket->getDateInstall());
            array_push(	$list, $ticket->getDateArchive());
            $dbTickets[$ticket->getNTicket()] = $list;
        }

        return $dbTickets;
    }

    /**
     * @Route("/mise-a-jour", name="reseau_upload", methods={"GET","POST"})
     */
    public function index(Request $request, TicketReseauRepository $ticketReseauRepository): Response
    {
        $lastUpdatedDate = $ticketReseauRepository->findOneByLastUpdatedDate();
        //dd($lastUpdatedDate[0]['maxDate']);
        //dd($lastUpdatedDate);
        $ticketNoIncident = new TicketNoIncident();
        $form = $this->createForm(TicketNoIncidentFormType::class, $ticketNoIncident);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $ticketNoIncident->setEtat('traité');
            // persist object
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticketNoIncident);
            $entityManager->flush();
            // generate a success message
            $this->addFlash('success','Le magasin ' . $ticketNoIncident->getCodeMagasin() . ' a bien été enregistré.' );
            // redirect to current page
            return $this->redirectToRoute('reseau_upload');
        }

        // upload and extract excel file data
        $excelForm = $this->createForm(ExcelFileFormType::class);
        $excelForm->handleRequest($request);

        if($excelForm->isSubmitted() && $excelForm->isValid()) {
            
            $file = $excelForm['file']->getData();
            $fileName = $file->getClientOriginalName();
            $fileArray = explode('.', $fileName);
            if(str_contains($fileArray[0], 'Kit4G')) {

                $spreadSheet = IOFactory::load($file);
                $row = $spreadSheet->getActiveSheet()->removeRow(1);
                $sheetData = $row->toArray(null, true, true, true); 
                //dd($sheetData); 
                $dbTickets = $this->getData($ticketReseauRepository);
                //dd($dbTickets);
                foreach($sheetData as $key => $value) {
                    //dd(array_key_exists(441860, $dbTickets));
                    if(array_key_exists($value['B'], $dbTickets)) {
                        // get current reseau ticket contained in db
                        $ticketReseau = $ticketReseauRepository->find($value['B']);
                        //dd($ticketReseau);
                        //dd($dbTickets[$value['B']][0]);
                        if($dbTickets[$value['B']][0] == 'Ticket_ouvert') {
                            // update reseau ticket
                            $ticketReseau->setDateMaj(new \DateTime());
                            $this->getDoctrine()->getManager()->flush();
                            // generate a success message
                            $this->addFlash('warning','-- MISE A JOUR -- Le ticket '.$value['B'].' vient d\'être mis à jour | <strong>Ticket ouvert</strong> depuis  '.$dbTickets[$value['B']][1]->format('d/m/Y').' .');
                        } 
                        
                        if($dbTickets[$value['B']][0] == 'Ticket_traité') {
                            $ticketReseau->setDateMaj(new \DateTime());
                            $this->getDoctrine()->getManager()->flush();
                            // generate a success message
                            $this->addFlash('warning','-- MISE A JOUR -- Le ticket '.$value['B'].' vient d\'être mis à jour | <trong>Kit 4G installé</strong> depuis  '.$dbTickets[$value['B']][2]->format('d/m/Y').' .');
                        }

                        //clean temp ticket from temp tickets array
					    unset($dbTickets[$key]);

                    } else {
                        $ticketReseau = new TicketReseau();
                        //dd($value['A']);
                        $ticketReseau->setNTicket(strval($value['B']))
                                    ->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime($value['A']))->format('Y-m-d H:i:s')))
                                    ->setCodeIncident(strval($value['C']))
                                    ->setHistorique(strval($value['D']))
                                    ->setTypeMagasin(strval($value['E']))
                                    ->setCodeMagasin(strval($value['F']))
                                    ->setNomMagasin(strval($value['G']))
                                    ->setDescription(strval($value['H']))
                                    ->setCodeMaintneur(strval($value['I']))
                                    ->setDateMaj(new \DateTime())
                                    ->setEtatTicket('Ticket_ouvert');
                        // persist object
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($ticketReseau);
                        $entityManager->flush();
                        $this->addFlash('success', '-- NOUVEAU TICKET -- Le ticket '.$value['B'].' vient d\'être ajouté.');
                    }
                }

                // Traiter les tickets qui restent (tickets résolu)
                foreach($dbTickets as $key => $value) {
                    $ticketReseau = $ticketReseauRepository->find($key);
                    if($dbTickets[$key][0] == 'Ticket_ouvert') {
                        $ticketReseau->setDateMaj(new \DateTime())
                                    ->setDateArchive(new \DateTime())
                                    ->setEtatTicket('Ticket_archivé');
                        $this->getDoctrine()->getManager()->flush();
                        // generate a success message
                        $this->addFlash('success','-- TICKET RÉSOLU -- Le ticket '.$key.' est résolu sans intervention, il sera archivé automatiquement.');
                    }
                    
                    if($dbTickets[$key][0] == 'Ticket_traité') {
                        $ticketReseau->setDateMaj(new \DateTime())
                                    ->setEtatTicket('Ticket_a_fermer');
                        $this->getDoctrine()->getManager()->flush();
                        // generate a success message
                        $this->addFlash('danger','-- Kit 4G A RETIRER -- Le ticket '.$key.' est résolu, merci de retirer le Kit 4G en magasin.');
                    }
                    
                    if($dbTickets[$key][0] == 'Ticket_a_fermer') {
                        $ticketReseau->setDateMaj(new \DateTime());
                        $this->getDoctrine()->getManager()->flush();
                        // generate a success message
                        $this->addFlash('danger','-- Kit 4G A RETIRER -- Le ticket '.$key.' vient d\'être mis à jour | Merci de retirer le kit 4G du magasin.');
                    }

                    //clean temp ticket from temp tickets array
			        unset($dbTickets[$key]);
                }

                return $this->redirectToRoute('reseau_upload');

            } else {
                $this->addFlash('danger', 'Le nom du fichier n\'est pas valide. Merci de le vérifier. (Ex : FINAL_Rapport_Backlogs_Tickets_Reseau_Proxi_Kit4G_Prod.xlsx)');
            }

            return $this->redirectToRoute('reseau_upload');
        }

        return $this->render('ticket_reseau/upload.html.twig',[
            'lastUpdatedDate' => $lastUpdatedDate,
            'noIncidentForm' => $form->createView(),
            'excelForm' => $excelForm->createView(),
        ]);
    }

    /**
     * manage all ticket
     * @Route("/gestion-tickets", name="reseau_manage", methods={"GET"})
     */
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

    /**
     * get details of one ticket and add comment if necessary
     * @Route("/gestion-tickets/{id}", name="reseau_details", methods={"GET"})
     */
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

    /**
     * comment ticket
     * @Route("/gestion-tickets/comment/{id}", name="reseau_comment", methods={"GET","POST"})
     */
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

    /**
     * install ARS
     * @Route("/gestion-tickets/install/{id}", name="reseau_install", methods={"GET","POST"})
     */
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
            $this->addFlash('success','Le Ticket ' . $ticketReseau->getNTicket() . ' est traité : Une demande d\'installation d\'un kit 4G a été effectuée.');
            // redirect to current page
            return $this->redirectToRoute('reseau_manage');
        }

        return $this->render('ticket_reseau/install.html.twig', [
            'ticketReseau' => $ticketReseau,
            'installForm' => $form->createView()
        ]);
    }

    /**
     * recuperate ARS
     * @Route("/gestion-tickets/recuperate/{id}", name="reseau_recuperate", methods={"GET","POST"})
     */
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
            $this->addFlash('success','Le Ticket ' . $ticketReseau->getNTicket() . ' est traité : Une demande de récupération du kit 4G a été effectuée.');
            // redirect to current page
            return $this->redirectToRoute('reseau_manage');
        }

        return $this->render('ticket_reseau/recuperate.html.twig', [
            'ticketReseau' => $ticketReseau,
            'recuperateForm' => $form->createView()
        ]);
    }

    /**
     * get all recurring store details
     * @Route("/gestion-tickets/recurring/{codeMagasin}", name="reseau_recurring_details", methods={"GET"})
     */
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


