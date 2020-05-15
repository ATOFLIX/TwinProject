function selectionCheckbox() {
	var longueur = $(".cocher:checked").length;
	if (longueur > 1) {
		$('#afficher').prop('disabled', true);
		$('#supprimer').prop('disabled', false);
		$('#renommer').prop('disabled', true);
	} else if (longueur == 1) {
		$('#supprimer').prop('disabled', false);
		$('#afficher').prop('disabled', false);
		$('#renommer').prop('disabled', false);
	} else if (longueur == 0) {

		$('input#afficher').prop('disabled', true);
		$('input#supprimer').prop('disabled', true);
		$('#renommer').prop('disabled', true);
	}
}

$(document)
		.ready(
				function() {

					$('#afficher').prop("disabled", true);
					$('#supprimer').prop("disabled", true);
					$('#renommer').prop('disabled', true);
					$(".cocher").on("change", function() {
						selectionCheckbox();

					});

					$("#checkAll").click(
							function() {
								if ($("input:checkbox").not(this).prop(
										"checked", this.checked)) {
									selectionCheckbox();
								} else if ($("input:checkbox").prop("checked",
										this.unchecked)) {
									$('#supprimer').prop('disabled', true);
									$('#afficher').prop('disabled', true);
								}

							});
					$(".table")
							.DataTable(
									{
								        "language": {
								            "lengthMenu": "Montrer _MENU_ éléments par page",
								            "zeroRecords": "Aucune donnée dans le tableau",
								            "info": "Affichage de  la page _PAGE_ de _PAGES_",
								            "infoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
								            "infoFiltered": "(filtré à partir du  _MAX_ d'éléments total)",
								            "search":         "Rechercher :",
								            "paginate": {
									            "first":    "Premier",
									            "last":     "Dernier",
									            "next":     "Suivant",
									            "previous": "Précédent",
									        }
								        }
								        
								    } );

				});
