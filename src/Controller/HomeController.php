<?php

namespace App\Controller;

use App\Entity\TicketIbm;
use App\Entity\TicketReseau;
use App\Form\IBMXLSXFormType;
use App\Form\ReseauXLSXFormType;
use App\Repository\TicketIbmRepository;
use App\Repository\TicketReseauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Include PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

class HomeController extends AbstractController
{
    private function convertFRdatetimeToUS (string $datetime): string
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("/", $dateAndTime[0]);
        $time = explode(":", $dateAndTime[1]);
        if(count($time) == 2) {
            return $date[2] . "-" . $date[1] . "-" . $date[0] . " " . $dateAndTime[1] . ":00";
        }
        return  $date[2] . "-" . $date[1] . "-" . $date[0] . " " . $dateAndTime[1]; 
    } 

    private function convertUSdatetimeToFR(string $datetime): string
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("-", $dateAndTime[0]);
        return $date[2] . "/" . $date[1] . "/" . $date[0] . " " .$dateAndTime[1];
    }

    /**
     * get data in database
     */
    private function getReseauData(TicketReseauRepository $ticketReseauRepository): array
    {
        $dbTickets = array();
        $tickets = $ticketReseauRepository->findByOpenedTreatedAndToClose();

        foreach($tickets as $ticket) {
            $list = array();
            array_push(	$list, $ticket["etatTicket"]);
            array_push(	$list, $ticket["dateCreation"]);
            array_push(	$list, $ticket["dateInstall"]);
            array_push(	$list, $ticket["dateArchive"]);
            $dbTickets[$ticket["nTicket"]] = $list;
        }

        return $dbTickets;
    }

    private function getIbmData(TicketIbmRepository $ticketIbmRepository, string $etatTicket): array
    {
        $dbTickets = array();
        $tickets = $ticketIbmRepository->findByNoneResolvedOrResolvedTickets($etatTicket);

        foreach($tickets as $ticket) {
            array_push($dbTickets, $ticket["nIncident"]." ".$ticket["dateAffectation"]);
        }

        return $dbTickets;
    }

    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function index(Request $request, TicketReseauRepository $ticketReseauRepository, TicketIbmRepository $ticketIbmRepository): Response
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

        $lastUpdatedDateReseau = $ticketReseauRepository->findOneByLastUpdatedDate();
        $lastUpdatedDateIbm = $ticketIbmRepository->findOneByLastUpdatedDate();
        
        // upload and extract reseau excel file data
        $excelReseauForm = $this->createForm(ReseauXLSXFormType::class);
        $excelReseauForm->handleRequest($request);
        if($excelReseauForm->isSubmitted() && $excelReseauForm->isValid()) {
            
            $file = $excelReseauForm['file']->getData();
            $fileName = explode('.', $file->getClientOriginalName());
            if(str_contains($fileName[0], 'Kit4G')) {

                $spreadSheet = IOFactory::load($file);
                $row = $spreadSheet->getActiveSheet()->removeRow(1);
                $sheetData = $row->toArray(null, true, true, true); 
                //dd($sheetData); 
                $dbTickets = $this->getReseauData($ticketReseauRepository);
                //dd($dbTickets);
                foreach($sheetData as $key => $value) {
                    //dd(array_key_exists(441860, $dbTickets));
                    if(array_key_exists($value['B'], $dbTickets)) {
                        //dd($dbTickets[$value['B']][0]);
                        // get current reseau ticket contained in db
                        $ticketReseau = $ticketReseauRepository->find($value['B']);
                        //dd($ticketReseau);
                        if($dbTickets[$value['B']][0] == 'Ticket_ouvert') {
                            $ticketReseau->setDateMaj(new \DateTime());
                            $this->getDoctrine()->getManager()->flush();
                            // generate a success message
                            $this->addFlash('warning','-- MISE A JOUR -- Le ticket '.$value['B'].' vient d\'être mis à jour | Ticket ouvert depuis '.$this->convertUSdatetimeToFR($dbTickets[$value['B']][1]).' .');
                        
                        } elseif($dbTickets[$value['B']][0] == 'Ticket_traité') {
                            $ticketReseau->setDateMaj(new \DateTime());
                            $this->getDoctrine()->getManager()->flush();
                            // generate a success message
                            $this->addFlash('warning','-- MISE A JOUR -- Le ticket '.$value['B'].' vient d\'être mis à jour | Kit 4G installé depuis '.$this->convertUSdatetimeToFR($dbTickets[$value['B']][2]).' .');
                        
                        } else {
                            $this->addFlash('secondary', '-- INFORMATION -- Le ticket '.$value['B'].' a déjà été clôturé.');
                        }

                        //clean temp ticket from temp tickets array
					    unset($dbTickets[$value['B']]);
                    
                    } else {
                        $ticketReseau = new TicketReseau();
                        //dd($value['B']);
                        $ticketReseau->setNTicket(strval($value['B']))
                                    ->setDateCreation(new \DateTime(date("Y-m-d H:i:s", strtotime($this->convertFRdatetimeToUS($value['A'])))))
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
                        $this->addFlash('info','-- TICKET RÉSOLU -- Le ticket '.$key.' est résolu sans intervention, il sera archivé automatiquement.');
                    
                    } elseif($dbTickets[$key][0] == 'Ticket_traité') {
                        $ticketReseau->setDateMaj(new \DateTime())
                                    ->setEtatTicket('Ticket_a_fermer');
                        $this->getDoctrine()->getManager()->flush();
                        // generate a success message
                        $this->addFlash('danger','-- Kit 4G A RETIRER -- Le ticket '.$key.' est résolu, merci de retirer le Kit 4G en magasin.');
                    
                    } elseif($dbTickets[$key][0] == 'Ticket_a_fermer') {
                        $ticketReseau->setDateMaj(new \DateTime());
                        $this->getDoctrine()->getManager()->flush();
                        // generate a success message
                        $this->addFlash('danger','-- Kit 4G A RETIRER -- Le ticket '.$key.' vient d\'être mis à jour | Merci de retirer le kit 4G du magasin.');
                    
                    } else {
                        $this->addFlash('secondary', '-- INFORMATION -- Le ticket '.$key.' a déjà été clôturé.');
                    }

                    //clean temp ticket from temp tickets array
			        unset($dbTickets[$key]);
                }

            } else {
                $this->addFlash('danger', 'Le nom du fichier n\'est pas valide. Merci de le vérifier. (Ex : FINAL_Rapport_Backlogs_Tickets_Reseau_Proxi_Kit4G_Prod.xlsx)');
            }

            return $this->redirectToRoute('home');
        }

        // upload and extract ibm excel file data
        $excelIbmForm = $this->createForm(IBMXLSXFormType::class);
        $excelIbmForm->handleRequest($request);
        if($excelIbmForm->isSubmitted() && $excelIbmForm->isValid()) {

            $file = $excelIbmForm['file']->getData();
            $fileName = explode('.', $file->getClientOriginalName());
            if(str_contains($fileName[0], 'SERCA')) {

                $spreadSheet = IOFactory::load($file);
                $row = $spreadSheet->getActiveSheet()->removeRow(1, 3);
                $sheetData = $row->toArray(null, true, true, true); 
                //dd($sheetData); 
                $noneResolvedTickets = $this->getIbmData($ticketIbmRepository, 'NON_RESOLU');
                //dd($noneResolvedTickets); 
                $resolvedTickets = $this->getIbmData($ticketIbmRepository, 'RESOLU');
                //dd($resolvedTickets);

                foreach($sheetData as $key => $value) {
                    $ticketXlsx = $value['C'] . " " . $value['B'] .":00";
                    //dd($ticketXlsx);
                    //dd(in_array('954087 2020-12-07 10:37:00', $dbTickets));
                    if(in_array($ticketXlsx, $noneResolvedTickets)) {
                        $ticketIbm = $ticketIbmRepository->findOneBy([
                            'nIncident' => $value['C'],
                            'dateAffectation' => $value['B'] .":00"
                        ]);
                        //dd($ticketIbm);
                        $ticketIbm->setDescription($value['D'])
                                ->setEtatIncident($value['E'])
                                ->setImpact($value['F'])
                                ->setUrgence($value['G'])
                                ->setPriorite($value['H'])
                                ->setNbRelances($value['I'])
                                ->setIncidentAffectedAt($value['J'])
                                ->setNTache($value['K'])
                                ->setTacheAffectedAt($value['L'])
                                ->setSujetTache($value['M'])
                                ->setDetailsTache($value['N'])
                                ->setTypeEquipement($value['O'])
                                ->setTypeLogiciel($value['P'])
                                ->setNomEquipement($value['Q'])
                                ->setCodeMagasin($value['R'])
                                ->setNomMagasin($value['S'])
                                ->setTypeMagasin($value['T'])
                                ->setDateMaj(new \DateTime());
                        $this->getDoctrine()->getManager()->flush();
                        $this->addFlash('warning', '-- MISE A JOUR -- Le ticket '.$value['C'].' - '.$value['B'].' vient d\'être mis à jour.');
                    
                        // clear ticket from $noneResolvedTickets
                        unset($noneResolvedTickets[array_search($ticketXlsx, $noneResolvedTickets)]);
                    
                    } elseif(!in_array($ticketXlsx, $noneResolvedTickets)) {
                        // clear ticket from $resolvedTickets
                        unset($noneResolvedTickets[array_search($ticketXlsx, $noneResolvedTickets)]);
                        $this->addFlash('secondary', '-- INFORMATION -- Le ticket '.$value['C'].' - '.$value['B'].' a déjà été résolu.');
                    
                    } else {
                        $ticketIbm = new TicketIbm();
                        $ticketIbm->setNIncident($value['C'])
                            ->setDateAffectation(new \DateTime(date("Y-m-d H:i:s", strtotime($this->convertFRdatetimeToUS($value['B'])))))
                            ->setDateCreation(new \DateTime(date("Y-m-d H:i:s", strtotime($this->convertFRdatetimeToUS($value['A'])))))
                            ->setDescription($value['D'])
                            ->setEtatIncident($value['E'])
                            ->setImpact($value['F'])
                            ->setUrgence($value['G'])
                            ->setPriorite($value['H'])
                            ->setNbRelances($value['I'])
                            ->setIncidentAffectedAt($value['J'])
                            ->setNTache($value['K'])
                            ->setTacheAffectedAt($value['L'])
                            ->setSujetTache($value['M'])
                            ->setDetailsTache($value['N'])
                            ->setTypeEquipement($value['O'])
                            ->setTypeLogiciel($value['P'])
                            ->setNomEquipement($value['Q'])
                            ->setCodeMagasin($value['R'])
                            ->setNomMagasin($value['S'])
                            ->setTypeMagasin($value['T'])
                            ->setDateMaj(new \DateTime())
                            ->setEtatTicket('NON_RESOLU');
                        // persist object
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($ticketIbm);
                        $entityManager->flush();
                        $this->addFlash('success', '-- NOUVEAU TICKET -- Le ticket '.$value['C'].' - '.$value['B'].' vient d\'être ajouté.');
                    
                    }
                }

                //Traiter les tickets qui restent (tickets résolu)
                foreach($noneResolvedTickets as $key => $value) {
                    //mettre à jour pour tickets résolus
                    $ticket = explode(" ", $value);
                    $ticketIbm = $ticketIbmRepository->findOneBy([
                        'nIncident' => $ticket[0], 
                        'dateAffectation' => $ticket[1]." ".$ticket[2]
                    ]);
                    $ticketIbm->setDateMaj(new \DateTime())
                            ->setEtatTicket('RESOLU');
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('info', '-- TICKET RESOLU -- Le ticket '.$ticket[0].' - '.$this->convertUSdatetimeToFR($ticket[1]." ".$ticket[2]).' vient d\'être résolu.');
                    
                    //clear ticket from $noneResolvedTickets
                    unset($noneResolvedTickets[$key]);
                }

            } else {
                $this->addFlash('danger', 'Le nom du fichier n\'est pas valide. Merci de le vérifier. (Ex : Rapport_Backlog_SERCA_Proxi_Jour.xlsx)');
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('home/home.html.twig', [
            'openedTickets' => $openedTickets,
            'treatedTickets' => $treatedTickets,
            'closedTickets' => $closedTickets,
            'impactHigh' => $impactHigh,
            'impactMedium' => $impactMedium,
            'impactLow' => $impactLow,
            'urgenceHigh' => $urgenceHigh,
            'urgenceMedium' => $urgenceMedium,
            'urgenceLow' => $urgenceLow,
            'lastUpdatedDateReseau' => $lastUpdatedDateReseau,
            'lastUpdatedDateIbm' => $lastUpdatedDateIbm,
            'excelReseauForm' => $excelReseauForm->createView(),
            'excelIbmForm' => $excelIbmForm->createView(),
        ]);
    }
}
