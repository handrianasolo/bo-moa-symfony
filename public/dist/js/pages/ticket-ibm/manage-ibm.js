// DataTable
$(function() {
    let table = $('#tabticketsibm').DataTable({
        dom: 'lfrBtip',
        buttons: [ 
            {
                extend: 'csvHtml5',
                title: 'Rapport_Tickets_Backlog_Proxi_csv',
                text: 'Exporter en CSV',
                className: 'btn btn-warning',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19 ]
                }
            },
            {
                extend: 'excelHtml5',
                title: 'Rapport_Tickets_Backlog_Proxi_xlsx',
                text: 'Exporter en EXCEL',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Rapport_Tickets_Backlog_Proxi_pdf',
                text: 'Exporter en PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [ 0, 1, 3, 12, 5, 6, 8, 17, 18, 19 ]
                },
                orientation: 'landscape',
                pageSize: 'LEGAL'
            } 
        ],
        lengthMenu:[5,10,15,20],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
        },
        responsive: true,
        "columnDefs": [
            {
                // hide column impact in datatable
                "targets": [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16 ],
                "visible": false,
                "searchable": true
            },
        ],
        "infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " ticket(s) sur " + total + " tickets ouverts</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " tickets sur " + total + " tickets ouverts</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " tickets ouverts)</label>" : "");
        } 
    });

    // set datepicker in french language
    $.fn.datepicker.dates['fr'] = {
        days: ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
        daysShort: ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
        daysMin: ["D", "L", "M", "M", "J", "V", "S"],
        months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
        monthsShort: ["Janv.", "Févr.", "Mars", "Avril", "Mai", "Juin", "Juil.", "Août", "Sept.", "Oct.", "Nov.", "Déc."],
        today: "Aujourd'hui",
        monthsTitle: "Mois",
        clear: "Effacer",
        weekStart: 1,
        format: "dd/mm/yyyy"
    };

    // Bootstrap datepicker
    $('.input-daterange input').each(function() {
        $(this).datepicker({ 
            language: 'fr',
            todayHighlight: true,
            autoclose: true,
        });
    });

    
    let filterTickets = function(){
        // filter datatable between two dates 
        $.fn.dataTable.ext.search.push(
            function(settings, searchData, dataIndex) {

                if(settings.nTable.id !== 'tabticketsibm') {
                    return true;
                } 

                let min = $('#min-date').val();
                //console.log(min);
                let max = $('#max-date').val();
                //console.log(max);
                let dateAffectation = moment(searchData[1], "DD/MM/YYYY");
                //console.log(dateAffectation);

                if(
                    (min == "" || max == "") 
                    || (dateAffectation.isSameOrAfter(moment(min, "DD/MM/YYYY")) 
                    && dateAffectation.isSameOrBefore(moment(max, "DD/MM/YYYY"))))
                {
                    return true;
                }
            }
        );

        // filter values in column impact
        $.fn.dataTable.ext.search.push(
            function(settings, searchData, dataIndex) {

                if(settings.nTable.id !== 'tabticketsibm') {
                    return true;
                } 

                let impacts = $('input:radio[name="impact"]:checked').map(function() {
                    return this.value;
                }).get();
            
                if(impacts.length === 0) {
                    return true;
                }
                
                // index value of column impact in table
                if(impacts.indexOf(searchData[5]) !== -1) {
                    return true;
                }

            }
        );
        
        // filter values in column urgence
        $.fn.dataTable.ext.search.push(
            function(settings, searchData, dataIndex) {
           
                if(settings.nTable.id !== 'tabticketsibm') {
                    return true;
                } 

                let urgences = $('input:radio[name="urgence"]:checked').map(function() {
                    return this.value;
                }).get();
            
            
                if (urgences.length === 0) {
                    return true;
                }
                
                // index value of column urgence in table
                if (urgences.indexOf(searchData[6]) !== -1) { 
                    return true;
                }
            
            }
        );

        // filter values in column nombres de relances
        $.fn.dataTable.ext.search.push(
            function(settings, searchData, dataIndex) {
           
                if(settings.nTable.id !== 'tabticketsibm') {
                    return true;
                } 

                let selectValue = parseInt($('#relances option:selected').val());
                //console.log("selected",selectValue);

                let nbRelances = parseInt(searchData[8]);
                //console.log("hired",nbRelances);

                // column nombre de relances in table
                if (isNaN(selectValue) || nbRelances > selectValue) { 
                    return true;
                }
            
            }
        );
       
        // drawing data filtered
        table.draw();
    }  

    // Event listener to the two range filtering
    $('#filter').on("click", function() {
        let min = $('#min-date').val();
        let max = $('#max-date').val();
        if (moment(min, "DD/MM/YYYY").isAfter(moment(max, "DD/MM/YYYY"))){
            $('.error').html('<label class="text-danger"><i class="fas fa-exclamation-triangle"></i> ERREUR DATE : La date de fin est antérieure à la date de début</label>');
        } else {
            $('.error').html('');
        }

        filterTickets();
    });

    // reset inputs, radio buttons and show initial table
    $('#reset').on('click', function(){
        $('input[name=min-date]').val("");
        $('input[name=max-date]').val("");
        $('input:radio:checked').each(function() { this.checked = false; });
        $("#relances").val($("#relances").data("default-value"));
        $('.error').html('');
        table.draw();
    });
});