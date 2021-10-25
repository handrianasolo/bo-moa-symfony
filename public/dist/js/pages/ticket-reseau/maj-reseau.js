// Import excel file
$(document).ready(function() {
    $('#file').on('change', function(){
        $('form').trigger('submit');
	});
	
	$('form').on('submit', function(event){
		event.preventDefault();
		$.ajax({
			url: "{{path('upload-xlsx')}}",
			type: "post",
			data: new FormData(this),
			contentType: false,
			processData: false,
			cache: false,
			error:function(err){
				console.error(err);
			},
			success:function(data){
				$('#file').val('');      
			},
			complete:function(){
				console.log("Request finished.");
			}
		});
	});
});