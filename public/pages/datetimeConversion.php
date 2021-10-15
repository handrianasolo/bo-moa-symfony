<?php

    function convertdatetimeFRtoUS ($datetime)
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("/", $dateAndTime[0]);
        $dateUS = $date[2] . "-" . $date[1] . "-" . $date[0] . " " . $dateAndTime[1];
        return $dateUS; 
    } 

    function convertdatetimeUStoFR ($datetime)
    {
        $dateAndTime = explode(" ", $datetime);	
        $date = explode("-", $dateAndTime[0]);
        return $date[2] . "/" . $date[1] . "/" . $dateAndTime[0];
    } 

?>