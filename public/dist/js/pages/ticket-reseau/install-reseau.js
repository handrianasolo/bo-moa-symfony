$(document).ready(function() {
    //On écoute le "click" sur le bouton ayant la classe "modal-trigger"
    $('.modal-install').click(function () {
        //On récupère l'url depuis la propriété "Data-target" de la balise html a
        url = $(this).attr('data-target');
        //on fait un appel ajax vers l'action symfony qui nous renvoie la vue
        $.get(url, function (data) {
            //on injecte le html dans la modale
            $('.modal-content').html(data);
            //on ouvre la modale
            $('#installkit').modal('show');
        });
    })
});