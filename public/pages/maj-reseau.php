<?php

require('database.php');
require('../../vendor/autoload.php');

$bd_tickets_reseau = array();

// tickets dans la base de données avant la mise à jour
$query= "SELECT `nTicket`,`etatTicket` FROM `ticketsreseau` 
        WHERE `ticketsreseau`.`etatTicket` = 'Ticket_ouvert' 
        OR `ticketsreseau`.`etatTicket` = 'Ticket_traité' 
        OR `ticketsreseau`.`etatTicket` = 'Ticket_a_fermer'";
$tickets = $bdd_connection->query($query);
while($ticket = $tickets->fetch()){
	$tick = array();
	array_push(	$tick, $ticket[1]);
	$bd_tickets_reseau[$ticket[0]] = $tick;
}

if(!empty($_FILES["excel-file"])){
    //recup extension du fichier
	$file_array = explode(".", $_FILES["excel-file"]["name"]);
	if( ( strpos($file_array[0], "Kit4G") ) && $file_array[1] == "xlsx"){
		
		$object = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES["excel-file"]["tmp_name"]);
		
		foreach($object->getWorksheetIterator() as $worksheet){
			$highestRow = $worksheet->getHighestRow();
            // une ligne dans le fichier
			for($row=2; $row<=$highestRow; $row++){ 
				
				$temp_ticket = strval($worksheet->getCellByColumnAndRow(1, $row)->getValue());
                //Ticket Excel Exist in BD
				if(array_key_exists($temp_ticket,$bd_tickets_reseau)){ 
					// Ticket ouvert
					if($bd_tickets_reseau[$temp_ticket][0] == 'Ticket_ouvert'){ 
						//mettre à jour la date de la dernière mise à jour							
						$query= "UPDATE `ticketsreseau` SET `dateMaj` = now() WHERE `ticketsreseau`.`nTicket` = '".$temp_ticket."'";
						if($results= $bdd_connection->query($query)){
							echo '<p> <label class="text-yellow">-- MISE A JOUR --</label> Le ticket <b>'.$temp_ticket.'</b> vient d\'être mis à jour <b> | </b>  <b class="text-aqua"> Ticket ouvert </b> depuis <b> '.convertdatetimeUStoFR($bd_tickets_reseau[$temp_ticket][1]).' </b>. </p>';
						}else{
							echo '<p>ERREUR SERVEUR</p>';
						}	
                    // Kit en magasin	
					}elseif($bd_tickets_reseau[$temp_ticket][0] == 'Ticket_traité'){ 
						//mettre à jour la date de la dernière mise à jour							
						$query= "UPDATE `ticketsreseau` SET `dateMaj` = now() WHERE `ticketsreseau`.`nTicket` = '".$temp_ticket."'";
						if($results= $bdd_connection->query($query)){
							echo '<p> <label class="text-yellow">-- MISE A JOUR --</label> Le ticket <b>'.$temp_ticket.'</b> vient d\'être mis à jour <b> | </b> <b class="text-green"> Kit 4G installé </b> depuis <b> '.convertdatetimeUStoFR($bd_tickets_reseau[$temp_ticket][2]).' </b>.</p>';
						}else{
							echo '<p>ERREUR BASE DE DONNEES</p>';
						}
					}else{
						echo '<p>AUCUN TICKET DANS LE FICHIER</p>';
					}
					//supprimer le ticket de la liste Temp BD
					unset($bd_tickets_reseau[$temp_ticket]);
				// nouveau ticket	
				}else{ 
					//Numéro Ticket
					$N_ticket = $bdd_connection->quote($temp_ticket);
					//Date création Ticket formatée
					$Data_creation_ticket = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					$Data_creation_ticket = convertdatetimeFRtoUS($Data_creation_ticket);
					$Data_creation_ticket = $bdd_connection->quote($Data_creation_ticket);
					//Code d'incident
					$Code_incident = $bdd_connection->quote($worksheet->getCellByColumnAndRow(2, $row)->getValue());
					//Historique
					$Historique = $bdd_connection->quote($worksheet->getCellByColumnAndRow(3, $row)->getValue());
					//Type du magasin
					$Type_magasin = $bdd_connection->quote($worksheet->getCellByColumnAndRow(4, $row)->getValue());
					//Code du magasin
					$Code_magasin = $bdd_connection->quote($worksheet->getCellByColumnAndRow(5, $row)->getValue());
					//Nom du magasin
					$Nom_magasin = $bdd_connection->quote($worksheet->getCellByColumnAndRow(6, $row)->getValue());
					//Description
					$Description = $bdd_connection->quote($worksheet->getCellByColumnAndRow(7, $row)->getValue());
					//Code_maintneur
					$Code_maintneur = $bdd_connection->quote($worksheet->getCellByColumnAndRow(8, $row)->getValue());

					try{

						if($Data_creation_ticket != "'--'"){ // les données ne sont pas null
					
							$query= "INSERT INTO `ticketsreseau` (`nTicket`, `codeIncident`, `dateCreation`, 
                                    `codeMagasin`, `nomMagasin`, `typeMagasin`, `codeMaintneur`, `description`, `historique`, 
                                    `dateMaj`, `etatTicket`) VALUES (".$N_ticket.", ".$Code_incident.", ".$Data_creation_ticket.",
                                    ".$Code_magasin.", ".$Nom_magasin.", ".$Type_magasin.", ".$Code_maintneur.", ".$Description.", 
                                    ".$Historique.", now(), 'Ticket_ouvert')";
				
							if($results= $bdd_connection->query($query)){
								echo '<p> <label class="text-green">-- NOUVEAU TICKET --</label> Le ticket <b>'.$temp_ticket.'
								</b> vient d\'être ajouter. </p>';
							}else{
								echo '<p>ERREUR SERVEUR</p>';
							}
						}

					}catch(Exception $e){
						echo '<p> <label class="text-info">-- TICKET EXISTANT --</label> Le ticket <b>'.$temp_ticket.'
							</b> est déjà archivé. </p>';
					}
				}
            }  
        }
		// Traiter les tickets qui restent (tickets résolu)
		foreach ($bd_tickets_reseau as $key => $value) 
		{
            // un ticket ouvert résolu sans installation de kit 4G
			if($bd_tickets_reseau[$key][0] == 'Ticket_ouvert'){ 
				//mettre à jour le ticket							
				$query= "UPDATE `ticketsreseau` SET `dateMaj` = now(),`dateArchive` = now(), `etatTicket` = 'Ticket_archivé' WHERE `ticketsreseau`.`nTicket` = '".$key."'";
                if($results= $bdd_connection->query($query)){
                    echo '<p> <label class="text-green">-- TICKET RÉSOLU --</label> Le ticket <b>'.$key.'</b> est résolu sans intervention, il sera archivé automatiquement.</p>';
                }else{
                    echo '<p>ERREUR BASE DE DONNEES</p>';
                }
            // un ticket traité résolu il faut retirer un kit 4G
			}elseif($bd_tickets_reseau[$key][0] == 'Ticket_traité'){ 
				//mettre à jour le ticket							
				$query= "UPDATE `ticketsreseau` SET `dateMaj` = now(), `etatTicket` = 'Ticket_a_fermer' WHERE `ticketsreseau`.`nTicket` = '".$key."'";
				if($results= $bdd_connection->query($query)){
                    echo '<p> <label class="text-red">-- Kit 4G A RETIRER --</label> Le ticket <b>'.$key.'</b> est résolu, merci de retirer le kit 4G en magasin.</p>';
                }else{
                    echo '<p>ERREUR BASE DE DONNEES</p>';
                }
            // un ticket traité résolu il faut retirer un kit 4G
			}elseif($bd_tickets_reseau[$key][0] == 'Ticket_a_fermer'){ 
				//mettre à jour le ticket							
				$query= "UPDATE `ticketsreseau` SET `dateMaj` = now() WHERE `ticketsreseau`.`nTicket` = '".$key."'";
				if($results= $bdd_connection->query($query)){
						echo '<p> <label class="text-red">-- Kit 4G A RETIRER --</label> 
								Le ticket <b>'.$key.'</b> vient d\'être mis à jour <b> | </b>  
								<b class="text-red"> Merci de retirer le kit 4G du magasin </b>. </p>';
					}else{
						echo '<p>ERREUR BASE DE DONNEES</p>';
					}
			} else {
				echo '<p>AUCUN TICKET DANS LE FICHIER</p>';
			}
			//supprimer le ticket de la liste Temp BD
			unset($bd_tickets_reseau[$key]);	
		}
    // fichier non Excel 
    } else {
		    echo '<label class="text-danger">Fichier non valide. 
		   	Merci de vérifier le nom et le format du fichier (Ex : FINAL_Rapport_Backlogs_Tickets_Reseau_Proxi_Kit4G_Prod.xlsx)</label>';  
    }  
}

function convertdatetimeFRtoUS ($datetime)
{
	$pieces = explode(" ", $datetime);	
	$pieces2 = explode("/", $pieces[0]);
	$dateUS = $pieces2[2] . "-" . $pieces2[1] . "-" . $pieces2[0] . " " . $pieces[1];
	return $dateUS; 
} 

function convertdatetimeUStoFR ($datetime)
{
	$pieces = explode(" ", $datetime);	
	$pieces2 = explode("-", $pieces[0]);
	return $pieces2[2] . "/" . $pieces2[1] . "/" . $pieces2[0];
} 

?>