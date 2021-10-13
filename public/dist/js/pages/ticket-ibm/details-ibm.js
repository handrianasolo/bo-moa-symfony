// DataTable
$(function () {
    $('#tabticketsdetails').DataTable({
        dom: 'lftrip',
        lengthMenu:[10,15,20,25],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
        },
        responsive: true,
        "infoCallback": function(settings, start, end, max, total, pre){
            return ((total !== 0) ? "<label class='text-blue'>Affichage de " + start + " à " + end + " ticket(s) sur " + total + " tickets ouverts</label>" : "<label class='text-blue'>Affichage de 0 à " + end + " tickets sur " + total + " tickets ouverts</label>")
               + ((total !== max) ? " <label class='text-blue'>(filtré à partir de " + max + " tickets ouverts)</label>" : "");
        }
    });
});