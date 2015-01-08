(function($) {
    var historique = [];
    var compteur = 0;
    var globalTimer;
    var tourMonEquipe = 0;
    var tourEquipeAdverse = 0;
    var toursEquipeBleue = [ 0, 2, 4, 6, 9, 10, 13, 14 ];
    var isDefineDefinirRole = false;
    $('.champion-search-input').hide();
    var message = "";

    function inArray(needle, haystack) {
        return (-1 != $.inArray(needle, haystack))
    }

    function isMonTour(etape, monEquipe) {

        if ('BLUE' == monEquipe) {
            return inArray(etape, toursEquipeBleue);
        }

        return ! inArray(etape, toursEquipeBleue);
    }

    function tourDeLEquipe(etape) {
        return (inArray(etape, toursEquipeBleue) ? 'BLUE' : 'VIOLET');
    }

    function showSuggestedChampions() {

        var contains = $('.champion-list .suggest');
        var containsNot = $('.champion-list .item-champion').not('.suggest');

        animate(contains, containsNot);
    }

    $(window).resize(function() {
        clearTimeout(globalTimer);
        globalTimer = setTimeout(showSuggestedChampions, 500);
    });

    $('.champion-list .item-champion').live('click', function() {

        $('.champion-search-input').attr("placeholder", "Chercher un champion...");

        var $this = $(this);

        if (compteur >= 16 || $this.hasClass('inselectionnable'))
            return false;



        var idChampion     = $this.data('id-champion');
        var $champion      = $('.pick-champion img', this);
        var couleurEquipe         = $(".form-search-champion input[type='radio']:checked").val();
        var divDestination = '.' + tourDeLEquipe(compteur).toLowerCase() + '-team';

        if (compteur < 6) {
            historique.push({'action':'BANNIR', 'id_champion':idChampion});


            if(isMonTour(compteur, couleurEquipe)) {
                $(divDestination + ' .thumbnail .banned-champion[data-id-champion=' + tourMonEquipe + ']').append($champion.clone());
                tourMonEquipe ++;
            }
            else {
                $(divDestination + ' .thumbnail .banned-champion[data-id-champion=' + tourEquipeAdverse + ']').append($champion.clone());
                tourEquipeAdverse ++;
            }

            if(tourMonEquipe == 3 && tourEquipeAdverse == 3) {
                tourMonEquipe = 0;
                tourEquipeAdverse = 0;
                if(couleurEquipe == 'BLUE') {
                    $('.my-team .thumbnail .pick-champion[data-id-champion=0]').next().show();
                    message = "Sélectionnez un champion choisi par votre équipe";
                }
                else {
                    message = "Sélectionnez un champion choisi par l'équipe adverse";
                }
            } else  if(isMonTour(compteur + 1, couleurEquipe )) {
                message = "Sélectionnez un champion banni par votre équipe";
            }
            else {
                message = "Sélectionnez un champion banni par l'équipe adverse";
            }


        }
        else {

            var equipe = (isMonTour(compteur, couleurEquipe) ? 'MOI' : 'EUX');

            historique.push({'action':'CHOISIR', 'equipe':equipe, 'id_champion':idChampion});
            $this.removeClass('selectionnable');
            if(equipe == 'MOI') {
                $(divDestination + ' .thumbnail .pick-champion[data-id-champion=' + tourMonEquipe + ']').append($champion.clone());
                tourMonEquipe ++;
            }
            else {
                $(divDestination + ' .thumbnail .pick-champion[data-id-champion=' + tourEquipeAdverse + ']').append($champion.clone());
                tourEquipeAdverse ++;
            }
            if(isMonTour(compteur + 1, couleurEquipe)) {
                $('.my-team .thumbnail .pick-champion[data-id-champion=' + parseInt(tourMonEquipe - 1) + ']').next().hide();
                $('.my-team .thumbnail .pick-champion[data-id-champion=' + tourMonEquipe + ']').next().show();
                message = "Sélectionnez un champion choisi par votre équipe";
            }
            else {
                $('.my-team .thumbnail .pick-champion[data-id-champion=' + parseInt(tourMonEquipe - 1) + ']').next().hide();
                message = "Sélectionnez un champion choisi par l'équipe adverse";
            }
        }

        $this.removeClass('selectionnable');
        $this.addClass('inselectionnable');



        $('.champion-list .item-champion').addClass('suggest');
        showSuggestedChampions();
        compteur ++;

        $('.message').text('');
        $('.message').append(message);

        if( $('.champion-list .inselectionnable').length == 16) {
            mettreAJour(false);
        }


    });

    $(".my-team input[type='button']").live('click', function() {
        role = $(this).prev().val();

        for(key in historique) {
            if(historique[key]['action'] == "DEFINIR_ROLE") {
                historique[key]= {'action':'DEFINIR_ROLE', 'role':role};
                isDefineDefinirRole= true;
                break;
            }
        }
        if(! isDefineDefinirRole) {
            historique.push({'action':'DEFINIR_ROLE', 'role':role});
        }
        $('.champion-search-input').attr("placeholder", "Champions suggérés :");

        mettreAJour(true);

    });

    $(".form-search-champion input[type='radio']").change(function() {

        $('.champion-search-input').next().remove();
        historique = [];
        compteur = 0;
        tourMonEquipe = 0;
        tourEquipeAdverse = 0;

        monEquipe = $(".form-search-champion input[type='radio']:checked").val();
        var message = ""
        $.ajax ({
            type: 'POST',
            dataType: "json",
            url: "",
            data: {'equipe':monEquipe},
            success: function(data){
                    $('.teambuilder').text('');
                    $('.form-search-champion').append(data.teambuilder);
                    $(".champion-search-input").show();
                    $('.champion-role').hide();
                    $('.champion-list').liveFilter($('.champion-search-input'),'.item-champion');
                    $('.champion-list .item-champion img').first().load(function() {
                    showSuggestedChampions();
                });
                if(monEquipe == 'BLUE') {
                    message = "Sélectionnez un champion banni par votre équipe";
                }
                else {
                    message = "Sélectionnez un champion banni par l'équipe adverse";
                }

                $('.message').text('');
                $('.message').append(message);
            }
        })
    });
    function mettreAJour(getSuggestion) {

        $.ajax ({
            type: 'POST',
            dataType: "json",
            url: "getsuggestionchampion",
            data: JSON.stringify(historique),
            success: function(data){
                if( getSuggestion == true) {
                    $('.champion-list .item-champion').removeClass('suggest');

                    nbSuggestions = data.suggestions.length;
                    for (var i = 0; i < nbSuggestions; i ++) {
                        $('.champion-list .item-champion[data-id-champion=' + data.suggestions[i].id + ']').addClass('suggest')
                    }
                      showSuggestedChampions();
                }
                else {

                    if(data.isEquipeOptimale) {
                        $('.message').text('');
                        $('.message').append('Votre équipe est optimale selon vos règles');
                    }
                    else {
                        $('.message').text('');
                        $('.message').append('Votre équipe n\'est  pas optimale selon vos règles');
                        $('.message').removeClass('alert-success').addClass('alert-danger');
                    }
                }
            }
        })
    }


    if($("#tm_teambuilderbundle_regle_typeRegle option:selected").val() != "") {
        var valeurRegle = $('#tm_teambuilderbundle_regle_typeRegle option:selected').val();
        $("." + valeurRegle).show();
    }
    else if($(".tm_teambuilder_regle_modifier .type-regle select[value!='']").val() != "") {
        $(".tm_teambuilder_regle_modifier .type-regle select[value!='']").show();

        var idTypeRegle = $(".tm_teambuilder_regle_modifier .type-regle select[value!='']").attr('id');

        $('.' + idTypeRegle).show();
        $("#tm_teambuilderbundle_regle_typeRegle option[value='"+ idTypeRegle +"']").attr('selected', true);
    }

    $('#tm_teambuilderbundle_regle_typeRegle').change(function() {
       var valeurRegle = $(this).val();
        $('.type-regle').hide();
        $(".type-regle option[value='']").attr('selected', true);
        if(null != valeurRegle){
            $("." + valeurRegle).show();
        }

    });

    //$(".champion-card").live('hover',function() {
    //    $(".bar", $(this)).each(function() {
    //        var t = $(this).data("lenght")
    //    });
    //});
    //$(".champion-card").live('hover',function() {
    //    $(".bar", $(this)).each(function() {
    //        console.log($(this));
    //        $(this)
    //            .data("origWidth", $(this).width())
    //            .width(0)
    //            .animate({
    //                width: $(this).data("origWidth")
    //            }, 1000);
    //    });
    //});
    //$(".bar").each(function() {
    //    console.log($(this));
    //    $(this)
    //        .data("origWidth", $(this).width())
    //        .width(0)
    //        .animate({
    //            width: $(this).data("origWidth")
    //        }, 100);
    //});
})(jQuery);
