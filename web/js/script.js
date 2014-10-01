$(function() {

    /*var selected;
    var otherSelects = new Array();
    var nbSelects;

    function test() {

        selected = $('#typeregle option:selected').val();

        $('#typeregle option').each(function(index) {
           if(selected != $(this).val()) {
                otherSelects[index] = $(this);
           }
        });

        nbSelects = otherSelects.length;

        for(var i = 1; i < nbSelects; i ++) {
            $('#tm_teambuilderbundle_regle_' + otherSelects[i].val()).remove();


        }
    }
    test();*/
    //console.log(otherSelects);


    var equipe = '';
    var historique = [];
    var compteur = 0;
    var tourMonEquipe = 0;
    var tourEquipeAdverse = 0;
    var toursEquipeBleue = [ 0, 2, 4, 6, 9, 10, 13, 14 ];
    $('#form div').eq(2).hide();

    $('recherche-champion').submit(function (e) {
        e.preventDefault();
    });

    function array_values(input) {
        var tmp_arr = [],
            key = '';

        if (input && typeof input === 'object' && input.change_key_case) {
            return input.values();
        }

        for (key in input) {
            tmp_arr[tmp_arr.length] = input[key];
        }

        return tmp_arr;
    }

    function inArray(needle, haystack) {
        return (-1 != $.inArray(needle, haystack))
    }


    function isMonTour(etape, monEquipe) {
        if ('BLEU' == monEquipe) {
            return inArray(etape, toursEquipeBleue);
        }

        return ! inArray(etape, toursEquipeBleue);
    }

    function tourDeLEquipe(etape)
    {
        return (inArray(etape, toursEquipeBleue) ? 'BLEU' : 'VIOLETTE');
    }

    $('.champion-selecteur .liste-champion li img').live('click', function(e) {

        e.preventDefault();
        var $this = $(this);

        if (compteur >= 16 || $this.hasClass('inselectionnable'))
            return false;

        var idChampion     = $(this).data('champion-id');
        var $champion      = $this;
        var couleurEquipe         = $('#recherche-champion input[type="radio"]:checked').val();
        var divDestination = '.' + tourDeLEquipe(compteur).toLowerCase();



        if (compteur < 6) {
            historique.push({'action':'BANNIR', 'id_champion':idChampion});
            $this.removeClass('selectionnable');

            if(isMonTour(compteur, couleurEquipe)) {
                $(divDestination + ' ul.bannis #champion-id-' + tourMonEquipe).append($champion.clone());
                tourMonEquipe ++;
            }
            else {
                $(divDestination + ' ul.bannis #champion-id-' + tourEquipeAdverse).append($champion.clone());
                tourEquipeAdverse ++;
        }
            if(tourMonEquipe == 3 && tourEquipeAdverse == 3) {
                tourMonEquipe = 0;
                tourEquipeAdverse = 0;
            }



        }
        else {

            var equipe = (isMonTour(compteur, couleurEquipe) ? 'MOI' : 'EUX');

            historique.push({'action':'CHOISIR', 'equipe':equipe, 'id_champion':idChampion});
            $this.removeClass('selectionnable');
            if(equipe == 'MOI') {
                $(divDestination + ' ul.selectionne #champion-id-' + tourMonEquipe).prepend($champion.clone());
                tourMonEquipe ++;
            }
            else {
                $(divDestination + ' ul.selectionne #champion-id-' + tourEquipeAdverse).prepend($champion.clone());
                tourEquipeAdverse ++;
            }
            if(isMonTour(compteur + 1, couleurEquipe)) {
                $('.mon-equipe #champion-id-' + parseInt(tourMonEquipe - 1) + ' select').hide();
                $('.mon-equipe #champion-id-' + parseInt(tourMonEquipe - 1) + ' button').hide();
                $('.mon-equipe #champion-id-' + tourMonEquipe + ' select').show();
                $('.mon-equipe #champion-id-' + tourMonEquipe + ' button').show();
            }
            else {
                $('.mon-equipe #champion-id-' + parseInt(tourMonEquipe - 1) + ' select').hide();
                $('.mon-equipe #champion-id-' + parseInt(tourMonEquipe - 1) + ' button').hide();
            }

        }

        $this.addClass('inselectionnable');

        compteur ++;
        if(compteur == 6 && isMonTour(compteur + 1, equipe) && couleurEquipe == 'BLEU') {
            $('#champion-id-0 select').show();
            $('#champion-id-0 button').show();
        }
    });

    $('.mon-equipe button').live('click', function() {
//        role = $(this).prev().val();
//
//        $.each(historique, function( key, value ) {
//
//            if(value['action'] == 'CREER_REGLE') {
//                delete historique[key];
//            }
//        });
//
//        historique = array_values(historique);
//
//        historique.push({'action':'CREER_REGLE', 'role':role, 'operation':'EGAL', 'priorite':1, 'nombre':1});
//        console.log(historique);
//        console.log('zdzqdq');
        console.log(historique);
        mettreAJour();

    });

    $('#recherche-champion input[type="radio"]').change(function() {

        $('.champion-selecteur div').remove();
        historique = [];
        compteur = 0;
        tourMonEquipe = 0;
        tourEquipeAdverse = 0;
        monEquipe = $('#recherche-champion input[type="radio"]:checked').val();

        $.ajax ({
            type: 'POST',
            dataType: "json",
            url: "http://teambuilder/app_dev.php/teambuilder/regle/appliquer",
            data: {'equipe':monEquipe},
            success: function(data){
                $('.champion-selecteur').append(data.teambuilder);
                $('#form div').eq(2).show();
                $('.role-champion').hide();
                $(' .selectionne button').hide();
                $('#list-to-filter').liveFilter($('#form_recherche'),'li img[data-champion-nom]');
            }
        })
    });
    function mettreAJour() {
        $.ajax ({
            type: 'POST',
            dataType: "json",
            url: "http://teambuilder/app_dev.php/teambuilder/ajax/getsuggestionchampion",
            data: JSON.stringify(historique),
            success: function(data){

                if(compteur > 6 && compteur < 16 ) {
                    $('.liste-champion ul li').removeClass('suggest');
                    nbSuggestions = data.suggestions.length;
                    for (var i=0; i<nbSuggestions; i++) {

                        $('.liste-champion ul li[data-champion-id=' + data.suggestions[i].id + ']').addClass('suggest');
                    }
                }
            }
        })
    }
});

