<?php
    $bddname='bo-moa-symfony';
    $BDD_hote='localhost';
    $BDD_user='root';
    $BDD_pass='';

	try{
		$bdd_connection = new PDO('mysql:host='.$BDD_hote.';dbname='.$bddname.';charset=utf8',$BDD_user,$BDD_pass);
		$bdd_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo 'Échec lors de la connexion : ' . $e->getMessage();
	}
?>