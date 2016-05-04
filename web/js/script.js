
$(document).ready(function() {
	/*
	*  Simple image gallery. Uses default settings
	*/


	$("#filtreAlbum").change(function(){
		var albumId = $(this).val();
		$(".allAlbums").hide("slow");
		$("#album"+albumId).show("slow");
	});


	$("#showAddImage").click(function(){
		$("#formAddImage").show();
		$(this).hide();
	});
	
	$('.fancybox').fancybox();




	$("#fileuploader").uploadFile({
		url:"/play/uploadfile",
		allowedTypes:"png,gif,jpg,jpeg",
		fileName:"myfile"
	});

});



// ajout de la classe JS à HTML
document.querySelector("html").classList.add('js');
 
// initialisation des variables
var fileInput  = document.querySelector( ".input-file" ),  
    button     = document.querySelector( ".input-file-trigger" ),
    the_return = document.querySelector(".file-return");
 
// action lorsque la "barre d'espace" ou "Entrée" est pressée
button.addEventListener( "keydown", function( event ) {
    if ( event.keyCode == 13 || event.keyCode == 32 ) {
        fileInput.focus();
    }
});
 
// action lorsque le label est cliqué
button.addEventListener( "click", function( event ) {
   fileInput.focus();
   return false;
});
 
// affiche un retour visuel dès que input:file change
fileInput.addEventListener( "change", function( event ) {  
    the_return.innerHTML = this.value;  
});