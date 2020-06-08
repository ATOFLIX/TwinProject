var seq=[];
function enregistrer(){			
		var socket=io.connect('http://localhost:8080/');
		
		socket.on("angle2", function (texte){
		//console.log(texte);
		seq.push(texte);
		console.log(seq);
		});	
		return false;	
	}
//https://stackoverflow.com/questions/4738595/how-do-i-delay-a-function-call-for-1-second
var envoi=function envoyer(){
	$("#data").attr("value",seq);	
	$("#formulaire").submit();
	
	return false;
	
}

function progressbarre(){
  $( "#progressbar" ).progressbar({
    value: false,
   
  });
}

$(document)
		.ready(
				function() {

					
					$("#debut").click(function(){
						progressbarre();enregistrer();
						if($('.message').length) {		
					           $('.message').css("opacity",1).fadeOut(1000);
					      
						}});
					
					
					$("#fin").click(function(){
						$("#progressbar").remove();
						
						setTimeout(envoi,1000);
						//alert("l'enregistrement est bientôt terminé");
					});
					
				});
