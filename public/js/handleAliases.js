$("#form_companyName").focusout(function () {
    let slug = slugify($("#form_companyName").val());
    if ($("#form_aliasNl").val().trim() === '') {
        $("#form_aliasNl").val(slug).trigger("change");

    }
    if ($("#form_aliasFr").val().trim() === '') {
        let slug = slugify($("#form_companyName").val());
        $("#form_aliasFr").val(slug).trigger("change");
    }
});

$("#form_aliasNl").on("change paste keyup input", function () {
    handleNlUrlState();
});

$("#form_aliasFr").on("change paste keyup input", function () {
    handleFrUrlState();
});

function handleNlUrlState() {
    if ($("#form_aliasNl").val().trim() != '') {
        $("#form_alias_url_nl").text('www.degroteverkeersquiz.be/quiz/'+$("#form_aliasNl").val());
    } else {
        $("#form_alias_url_nl").text('');
    }
}

function handleFrUrlState() {
    if ($("#form_aliasFr").val().trim() != '') {
        $("#form_alias_url_fr").text('www.quizdelaroute.be/quiz/'+$("#form_aliasFr").val());
    } else {
        $("#form_alias_url_fr").text('');
    }
}