/** JS Mettre à jour les tickets réseau **/
// Import excel file
$(function(){
    $('#excel-file').on("change", function(){
        $('#excel-form').trigger("submit");
	});
	
	$('#excel-form').on('submit', function(event){
		event.preventDefault();
		$.ajax({
			url: "../../../../pages/maj-reseau.php",
			method: "POST",
			data: new FormData(this),
			contentType: false,
			processData: false,
			success: function(data){
				
				$('#excel-result').className = "card-body";
				$('#excel-result').html(data);
				$('#excel-file').val('');

				/* var d = new Date();
				var day = d.getDate().toString().padStart(2, "0");
				var month = (d.getMonth()+1).toString().padStart(2, "0");
				var year = d.getFullYear();
				var datenow = day + "/" + month + "/" + year;
				document.getElementById("divMaj").className = "card-body card-content bg-success d-flex";
				document.getElementById("dateMaj").innerHTML = "<b>" + datenow + "</b>"; */
            },
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
				alert("Status: " + textStatus); 
				alert("Error: " + errorThrown); 
			}
		});
	});
});