// Import excel file
$(function(){
    $('#excel-file').on("change", function(){
        $('#excel-form').trigger("submit");
	});
	
	$('#excel-form').on('submit', function(event){
		event.preventDefault();
		$.ajax({
			url: "{{path('reseau_upload')}}",
			method: "POST",
			data: new FormData(this),
			contentType: false,
			processData: false,
			success: function(data){
				$('#excel-file').val('');
            }
		});
	});
});