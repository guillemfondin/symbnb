$('#add-image').click(function() {
    //Récupération du futur champs à créer
    const index = +$('#widgets-counter').val();
    //Récupération du prototype des entrées (générées dans le code source par symfony)
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    //Injection le code au seins de la div
    $('#annonce_images').append(tmpl);
    $('#widgets-counter').val(index + 1)
    //Gestion du bouton suppr
    handleDeleteButton();
});
function handleDeleteButton() {
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        $(target).remove();
    })
}
function updateCounter() {
    const count = +$('#annonce_images div.form-group').length;
    $('#widgets-counter').val(count)
}
updateCounter();
handleDeleteButton();