// Handle company name change => only when alias empty
$("#form_companyName").focusout(function () {

    let slug = slugify($("#form_companyName").val());

    if ($("#form_aliasNl").val().trim() === '') {
        $("#form_aliasNl").val(slug);
    }

    if ($("#form_aliasFr").val().trim() === '') {
        $("#form_aliasFr").val(slug);
    }
});

$('.btn-copy-url').click(
    function () {
        var inputGroup = $(this).closest('.input-group');
        var fullUrl = inputGroup.find('.input-group-prepend .input-group-text').text() + inputGroup.find('input').val();

        var el = document.createElement('textarea');
        el.value = fullUrl;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
);
