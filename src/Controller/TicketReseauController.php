<?php

namespace App\Controller;

use App\Entity\TicketNoIncident;
use App\Entity\TicketReseau;
use App\Form\ArsInstallFormType;
use App\Form\ArsRecupFormType;
use App\Form\CommentFormType;
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
    /**
     * @Route("/mise-a-jour", name="reseau_upload", methods={"GET","POST"})
     */
    public function index(Request $request, TicketReseauRepository $ticketReseauRepository): Response
    {

        $dbTicketsTemp = array();
        $dbTicketsRepository = $ticketReseauRepository->findByOpenedTreatedAndToClose();

        // tickets in database before updating
        foreach($dbTicketsRepository as $ticket) {
            $temp = array();
            array_push($temp, $ticket['etatTicket']);
            array_push($temp, $ticket['dateCreation']);
            array_push($temp, $ticket['dateInstall']);
            array_push($temp, $ticket['dateArchive']);
            $dbTicketsTemp[$ticket['nTicket']] = $temp;
        }

        if (!empty($_FILES["excel-file"])){
            // get file extension
            $file_array = explode(".", $_FILES["excel-file"]["name"]);
            if ((strpos($file_array[0], "Kit4G") ) && $file_array[1] == "xlsx") {
                
                $spreadsheet = IOFactory::load($_FILES["excel-file"]["tmp_name"]);
                
                foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow();
                    // une ligne dans le fichier
                    for ($row = 2; $row <= $highestRow; $row++) { 
                        
                        $ticketTemp = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        //Ticket Excel Exist in BD
                        if (array_key_exists($ticketTemp, $dbTicketsTemp)) { 
                            // Ticket ouvert
                            if ($dbTicketsTemp[$ticketTemp][0] == 'Ticket_ouvert') { 
                                //mettre à jour la date de la dernière mise à jour	
                                $ticketReseau = $ticketReseauRepository->find($ticketTemp);
                                $ticketReseau->setDateMaj(new \DateTime());	
                                $this->getDoctrine()->getManager()->flush();					
                                $this->addFlash('warning', '<label class="text-yellow">-- MISE A JOUR --</label> Le ticket <strong>'.$ticketTemp.'</strong> vient d\'être mis à jour <strong> | </strong>  <strong class="text-aqua"> Ticket ouvert </strong> depuis <strong> '. $this->convertdatetimeUStoFR($dbTicketsTemp[$ticketTemp][1]).' </strong>');
                                return $this->redirectToRoute('reseau_upload');
                            }
                            
                            if ($dbTicketsTemp[$ticketTemp][0] == 'Ticket_traité') { 
                                //mettre à jour la date de la dernière mise à jour		
                                $ticketReseau = $ticketReseauRepository->find($ticketTemp);
                                $ticketReseau->setDateMaj(new \DateTime());		
                                $this->getDoctrine()->getManager()->flush();				
                                $this->addFlash('warning', '<label class="text-yellow">-- MISE A JOUR --</label> Le ticket <strong>'.$ticketTemp.'</strong> vient d\'être mis à jour <strong> | </strong>  <strong class="text-green"> Kit 4G installé </strong> depuis <strong> '. $this->convertdatetimeUStoFR($dbTicketsTemp[$ticketTemp][2]).' </strong>');					
                                return $this->redirectToRoute('reseau_upload');
                            
                            }

                            //supprimer le ticket de la liste Temp BD
                            unset($dbTicketsTemp[$ticketTemp]);

                        // nouveau ticket	
                        } else { 

                            $dateCreation = $this->convertdatetimeFRtoUS($worksheet->getCellByColumnAndRow(0, $row)->getValue());
                            $codeIncident = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                            $historique = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                            $typeMagasin = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                            $codeMagasin = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                            $nomMagasin = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            $description = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                            $codeMaintneur = $worksheet->getCellByColumnAndRow(8, $row)->getValue();

                            if (!str_contains($dateCreation, '--')) {
                                    
                                $ticketReseau = new TicketReseau();
                                $ticketReseau->setNTicket($ticketTemp)
                                            ->setDateCreation(\DateTime::createFromFormat('Y-m-d H:i:s', $dateCreation))
                                            ->setCodeIncident($codeIncident)
                                            ->setHistorique($historique)
                                            ->setTypeMagasin($typeMagasin)
                                            ->setCodeMagasin($codeMagasin)
                                            ->setNomMagasin($nomMagasin)
                                            ->setDescription($description)
                                            ->setCodeMaintneur($codeMaintneur)
                                            ->setDateMaj(new \DateTime())
                                            ->setEtatTicket('Ticket_ouvert');
                                
                                if ($ticketReseauRepository->find($ticketReseau->getNTicket())) {
                                    $this->addFlash('info', '<label class="text-info">-- TICKET EXISTANT --</label> Le ticket <strong>'.$ticketTemp.'</b> est déjà archivé.');
                                    return $this->redirectToRoute('reseau_upload');
                                }

                                // persist object
                                $entityManager = $this->getDoctrine()->getManager();
                                $entityManager->persist($ticketReseau);
                                $entityManager->flush();
                                $this->addFlash('success', '<label class="text-green">-- NOUVEAU TICKET --</label> Le ticket <strong>'.$ticketTemp.'</b> vient d\'être ajouté.');
                                return $this->redirectToRoute('reseau_upload');
                            }
                        }
                    }  
                }
                // Traiter les tickets qui restent (tickets résolu)
                foreach ($dbTicketsTemp as $key => $value) 
                {
                    // un ticket ouvert résolu sans installation de kit 4G
                    if($dbTicketsTemp[$key][0] == 'Ticket_ouvert'){ 
                        //mettre à jour le ticket	
                        $ticketReseau = $ticketReseauRepository->find($key);
                        $ticketReseau->setDateMaj(new \DateTime());
                        $ticketReseau->setDateArchive(new \DateTime());
                        $ticketReseau->setEtatTicket('Ticket_archivé');	
                        
                        $this->getDoctrine()->getManager()->flush();
                        $this->addFlash('success', '<label class="text-green">-- TICKET RÉSOLU --</label> Le ticket <strong>'.$key.'</strong> est résolu sans intervention, il sera archivé automatiquement.');
                        return $this->redirectToRoute('reseau_upload');
                    }

                    // un ticket traité résolu il faut retirer un kit 4G
                    if ($dbTicketsTemp[$key][0] == 'Ticket_traité') { 
                        //mettre à jour le ticket
                        $ticketReseau = $ticketReseauRepository->find($key);
                        $ticketReseau->setDateMaj(new \DateTime());
                        $ticketReseau->setEtatTicket('Ticket_a_fermer');	
                        
                        $this->getDoctrine()->getManager()->flush();	
                        $this->addFlash('danger', '<label class="text-red">-- Kit 4G A RETIRER --</label> Le ticket <b>'.$key.'</b> est résolu, merci de retirer le kit 4G en magasin.');						
                        return $this->redirectToRoute('reseau_upload');
                    }

                    // un ticket traité résolu il faut retirer un kit 4G
                    if ($dbTicketsTemp[$key][0] == 'Ticket_a_fermer') { 
                        //mettre à jour le ticket	
                        $ticketReseau = $ticketReseauRepository->find($key);
                        $ticketReseau->setDateMaj(new \DateTime());
                        
                        $this->getDoctrine()->getManager()->flush();	
                        $this->addFlash('danger', '<label class="text-red">-- Kit 4G A RETIRER --</label> 
                        Le ticket <b>'.$key.'</b> vient d\'être mis à jour <b> | </b>  
                        <b class="text-red"> Merci de retirer le kit 4G du magasin </b>.');	
                        return $this->redirectToRoute('reseau_upload');											
                    } 

                    //supprimer le ticket de la liste Temp BD
                    unset($dbTicketsTemp[$key]);	
                }
            
                // fichier non Excel 
            }

            $this->addFlash('danger', '<label class="text-danger">Fichier non valide. 
            Merci de vérifier le nom et le format du fichier (Ex : FINAL_Rapport_Backlogs_Tickets_Reseau_Proxi_Kit4G_Prod.xlsx)</label>');
            return $this->redirectToRoute('reseau_upload');
        }

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
            'noIncidentForm' => $form->createView(),
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
            $this->addFlash('success','Le Ticket ' . $ticketReseau->getNTicket() . ' est traité : Une demande d\'installation d\'un kit 4G a été effectuée');
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
            $this->addFlash('success','Le Ticket ' . $ticketReseau->getNTicket() . ' est traité : Une demande de récupération du kit 4G a été effectuée');
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

    protected function convertdatetimeFRtoUS(string $datetime)
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("/", $dateAndTime[0]);
        return $date[2] . "-" . $date[1] . "-" . $date[0] . " " . $dateAndTime[1];
    } 

    protected function convertdatetimeUStoFR(string $datetime)
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("-", $dateAndTime[0]);
        return $date[2] . "/" . $date[1] . "/" . $date[0] . " " . $dateAndTime[1];
    } 
}


