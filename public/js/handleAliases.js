// Make sure to init the URLs
handleNlUrlState();
handleFrUrlState();

// Handle company name change => only when alias empty
$("#form_companyName").focusout(function () {

    let slug = slugify($("#form_companyName").val());

    if ($("#form_aliasNl").val().trim() === '') {
        $("#form_aliasNl").val(slug);
        handleNlUrlState();
    }

    if ($("#form_aliasFr").val().trim() === '') {
        $("#form_aliasFr").val(slug);
        handleFrUrlState();
    }
});

// Handle NL alias changes => always update URL
$("#form_aliasNl").focusout(function () {
    handleNlUrlState();
});

// Handle FR alias change => always change URL
$("#form_aliasFr").focusout(function () {
    handleFrUrlState();
});

// Set the Nl URL
function handleNlUrlState() {
    let aliasNl = $("#form_aliasNl").val().trim();

    if (aliasNl !== '') {
        $("#form_alias_url_nl").text('www.degroteverkeersquiz.be/quiz/' + aliasNl);
    } else {
        $("#form_alias_url_nl").text('');
    }
}

// Set the Fr URL
function handleFrUrlState() {
    let aliasFr = $("#form_aliasFr").val().trim();

    if (aliasFr !== '') {
        $("#form_alias_url_fr").text('www.quizdelaroute.be/quiz/' + aliasFr);
    } else {
        $("#form_alias_url_fr").text('');
    }
}
