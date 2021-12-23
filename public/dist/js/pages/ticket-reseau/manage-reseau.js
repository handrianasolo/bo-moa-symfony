// DataTable
$(function () {
    $('#tabticketouvert').DataTable({
        dom: 'lfrBtip',
		buttons: [ 
            {
                extend: 'csvHtml5',
                title: 'Rapport_Tickets_Ouverts_Backlog_Proxi_csv',
                text: 'Exporter en CSV',
                className: 'btn btn-warning',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 7, 8, 9, 10, 16 ]
                }
            },
            {
                extend: 'excelHtml5',
                title: 'Rapport_Tickets_Ouverts_Backlog_Proxi_xlsx',
                text: 'Exporter en EXCEL',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 7, 8, 9, 10, 16 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Rapport_Tickets_Ouverts_Backlog_Proxi_pdf',
                text: 'Exporter en PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 7, 8, 9, 10, 16 ]
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
                "targets": [ 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 ],
                "visible": false,
                "searchable": true
            },
        ],
		"infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " ticket(s) sur " + total + " tickets ouverts</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " tickets sur " + total + " tickets ouverts</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " tickets ouverts)</label>" : "");
        }
	});
	
	$('#tabtickettraite').DataTable({
        dom: 'lfrBtip',
		buttons: [ 
            {
                extend: 'csvHtml5',
                title: 'Rapport_Tickets_Traités_Backlog_Proxi_csv',
                text: 'Exporter en CSV',
                className: 'btn btn-warning',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 7, 8, 9, 10, 13, 15 ]
                }
            },
            {
                extend: 'excelHtml5',
                title: 'Rapport_Tickets_Traités_Backlog_Proxi_xlsx',
                text: 'Exporter en EXCEL',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 7, 8, 9, 10, 13, 15 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Rapport_Tickets_Traités_Backlog_Proxi_pdf',
                text: 'Exporter en PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 7, 8, 9, 13, 15 ]
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
                "targets": [ 7, 8, 9, 10, 11, 12, 13, 14, 15 ],
                "visible": false,
                "searchable": true
            },
        ],
		"infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " ticket(s) sur " + total + " tickets traités</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " tickets sur " + total + " tickets traités</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " tickets traités)</label>" : "");
        }
	});
	
	$('#tabticketcloturer').DataTable({
        dom: 'lfrBtip',
		buttons: [ 
            {
                extend: 'csvHtml5',
                title: 'Rapport_Tickets_A_Cloturer_Backlog_Proxi_csv',
                text: 'Exporter en CSV',
                className: 'btn btn-warning',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 8, 9, 10, 11, 13, 14, 15, 16 ]
                }
            },
            {
                extend: 'excelHtml5',
                title: 'Rapport_Tickets_A_Cloturer_Backlog_Proxi_xlsx',
                text: 'Exporter en EXCEL',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 8, 9, 10, 11, 13, 14, 15, 16 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Rapport_Tickets_A_Cloturer_Backlog_Proxi_pdf',
                text: 'Exporter en PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 8, 9, 10, 13, 14, 15, 16 ]
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
                "targets": [ 8, 9, 10, 11, 12, 13, 14, 15, 16 ],
                "visible": false,
                "searchable": true
            },
        ],
		"infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " ticket(s) sur " + total + " tickets à clôturer</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " tickets sur " + total + " tickets à clôturer</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " tickets à clôturer)</label>" : "");
        }
    });

    $('#tabticketnoincident').DataTable({
        dom: 'lfrBtip',
        buttons: [ 
            {
                extend: 'csvHtml5',
                title: 'Rapport_Tickets_A_Cloturer_Backlog_Proxi_csv',
                text: 'Exporter en CSV',
                className: 'btn btn-warning',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 7, 8, 9, 10 ]
                }
            },
            {
                extend: 'excelHtml5',
                title: 'Rapport_Tickets_A_Cloturer_Backlog_Proxi_xlsx',
                text: 'Exporter en EXCEL',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 7, 8, 9, 10 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Rapport_Tickets_A_Cloturer_Backlog_Proxi_pdf',
                text: 'Exporter en PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 7, 8, 9, 10 ]
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
                "targets": [ 2, 7, 8, 9, 10 ],
                "visible": false,
                "searchable": true
            },
        ],
        "infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " kit(s) sur " + total + " kits à retirer au total</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " kits sur " + total + " kits à retirer au total</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " kit à retirer au total)</label>" : "");
        }
    });

    $('#tabstores').DataTable({
        dom: 'lfrBtip',
        lengthMenu:[5,10,15,20],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
        },
        responsive: true,
        "infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " magasin(s) sur " + total + " magasins</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " magasins sur " + total + " magasins</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " magasins)</label>" : "");
        }
    });
});