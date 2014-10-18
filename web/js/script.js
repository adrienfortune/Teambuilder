(function($) {
    var historique = [];
    var compteur = 0;
    var globalTimer;
    var tourMonEquipe = 0;
    var tourEquipeAdverse = 0;
    var toursEquipeBleue = [ 0, 2, 4, 6, 9, 10, 13, 14 ];
    var isDefineDefinirRole = false;
    $('.champion-search-input').hide();
//    $('.suggestion-button').hide();

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

    $('.champion-list .item-champion ').live('click', function() {

        $('.champion-search-input').attr("placeholder", "Chercher un champion...");

        var $this = $(this);

        if (compteur >= 16 || $this.hasClass('inselectionnable'))
            return false;

        var idChampion     = $this.data('id-champion');
        var $champion      = $('img', this);
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
                }
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
            }
            else {
                $('.my-team .thumbnail .pick-champion[data-id-champion=' + parseInt(tourMonEquipe - 1) + ']').next().hide();
            }
        }

        $this.removeClass('selectionnable');
        $this.addClass('inselectionnable');
        $('.champion-list .item-champion').addClass('suggest');
        showSuggestedChampions();
        compteur ++;

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

        mettreAJour();

    });

    $(".form-search-champion input[type='radio']").change(function() {

        $('.champion-search-input').next().remove();
        historique = [];
        compteur = 0;
        tourMonEquipe = 0;
        tourEquipeAdverse = 0;

        monEquipe = $(".form-search-champion input[type='radio']:checked").val();

        $.ajax ({
            type: 'POST',
            dataType: "json",
            url: "appliquer",
            data: {'equipe':monEquipe},
            async: false,
            success: function(data){
                $('.form-search-champion').append(data.teambuilder);
                $(".champion-search-input").show();
                $('.champion-role').hide();
                $('.champion-list').liveFilter($('.champion-search-input'),'.item-champion');
                $('.champion-list .item-champion img').first().load(function() {
                    showSuggestedChampions();
                });
            }
        })
    });
    function mettreAJour() {

        $.ajax ({
            type: 'POST',
            dataType: "json",
            url: "getsuggestionchampion",
            data: JSON.stringify(historique),
            success: function(data){

                if(compteur >= 6 && compteur < 16 ) {

                    $('.champion-list .item-champion').removeClass('suggest');

                    nbSuggestions = data.suggestions.length;

                    for (var i = 0; i < nbSuggestions; i ++) {
                        $('.champion-list .item-champion[data-id-champion=' + data.suggestions[i].id + ']').addClass('suggest')
                    }

                    showSuggestedChampions();
                }
            }
        })
    }
})(jQuery);
