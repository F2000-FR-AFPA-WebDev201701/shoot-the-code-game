// Switch entre le formulaire de connexion et d'inscription

$('#connexion a').click(function (event) {
    event.preventDefault();
    $('#connexion').addClass('hidden');
    $('#inscription').removeClass('hidden');
});

$('#inscription a').click(function (event) {
    event.preventDefault();
    $('#inscription').addClass('hidden');
    $('#connexion').removeClass('hidden');
});

function refreshList(view) {
    $('#multiModal').html(view);
}
;