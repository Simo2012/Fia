/**** Fork de la révision 3.0 du plugin jRating ****/

function rateInfo(rate, rateMax)
{
    return rate +' <span class="maxRate">/ '+rateMax+'</span>';
}

(function($) {
    $.fn.jRating = function(op) {
        var defaults = {
            /** String vars **/
            bigStarsPath : '/bundles/sceau/images/site/questionnaire/jRating_stars.png', // Chemin de l'image des grandes étoiles
            smallStarsPath : '/bundles/sceau/images/site/questionnaire/jRating_small_stars.png', 	// Chemin de l'image des petites étoiles
            type : 'big', 										// 'small' pour de petites étoiles ou 'big' pour de grandes étoiles
            inputID : '', 										// identifiant de l'input hidden qui va recevoir la valeur sélectionné par l'utilisateur

            /** Boolean vars **/
            step:false, 			// Si true, le remplissage se fait de manière compléte étoile par étoile
            isDisabled:false,
            showRateInfo: true,		// Si true, affiche une bulle d'aide quand on passe la souris sur une étoile. Pour modifier le contenu, il faut redéfinir la fonction rateInfo(rate, rateMax)
            canRateAgain : false,	// Si true, on peut noter un nombre x de fois défini par nbRates
            canRateInfinite : true, // Si true, on peut voter un nombre infini de fois

            /** Integer vars **/
            length: 5, 				// Nombre d'étoile
            decimalLength : 0, 		// Nombre de décimales. Max 3. Si égal à -1, on arrondie au demi-point près. Ex : 1.8 = 2; 5.2 = 5
            rateMax : 5, 			// Note maximale
            rateMinSelect : 0,		// Note minimale sélectionnable
            rateInfosX : -45, 		// Position relative X de l'info box
            rateInfosY : 5, 		// Position relative Y de l'info box
            nbRates : 1,

            /** Functions **/
            onSuccess : null,
            onError : null
        };

        if(this.length>0)
            return this.each(function() {
                /*vars*/
                var opts = $.extend(defaults, op),
                    newWidth = 0,
                    starWidth = 0,
                    starHeight = 0,
                    bgPath = '',
                    hasRated = false,
                    globalWidth = 0,
                    nbOfRates = opts.nbRates;

                if($(this).hasClass('jDisabled') || opts.isDisabled)
                    var jDisabled = true;
                else
                    var jDisabled = false;

                getStarWidth();
                $(this).height(starHeight);

                var average = parseFloat($(this).attr('data-average')), // get the average of all rates
                    idBox = parseInt($(this).attr('data-id')), // get the id of the box
                    widthRatingContainer = starWidth*opts.length, // Width of the Container
                    widthColor = average/opts.rateMax*widthRatingContainer, // Width of the color Container

                    quotient =
                        $('<div>',
                            {
                                'class' : 'jRatingColor',
                                css:{
                                    width:widthColor
                                }
                            }).appendTo($(this)),

                    average =
                        $('<div>',
                            {
                                'class' : 'jRatingAverage',
                                css:{
                                    width:0,
                                    top:- starHeight
                                }
                            }).appendTo($(this)),

                    jstar =
                        $('<div>',
                            {
                                'class' : 'jStar',
                                css:{
                                    width:widthRatingContainer,
                                    height:starHeight,
                                    top:- (starHeight*2),
                                    background: 'url('+bgPath+') repeat-x'
                                }
                            }).appendTo($(this));


                $(this).css({width: widthRatingContainer,overflow:'hidden',zIndex:1,position:'relative'});

                if(!jDisabled)
                    $(this).unbind().bind({
                        mouseenter : function(e){
                            var realOffsetLeft = findRealLeft(this);
                            var relativeX = e.pageX - realOffsetLeft;
                            if (opts.showRateInfo)
                            {
                                var tooltip =
                                    $('<p>',{
                                        'class' : 'jRatingInfos',
                                        html : rateInfo(relativeX, opts.rateMax),
                                        css : {
                                            top: (e.pageY + opts.rateInfosY),
                                            left: (e.pageX + opts.rateInfosX)
                                        }
                                    }).appendTo('body').show();
                            }
                        },
                        mouseover : function(e){
                            $(this).css('cursor','pointer');
                        },
                        mouseout : function(){
                            $(this).css('cursor','default');
                            if(hasRated) average.width(globalWidth);
                            else average.width(0);
                        },
                        mousemove : function(e){
                            var realOffsetLeft = findRealLeft(this);
                            var relativeX = e.pageX - realOffsetLeft;
                            if(opts.step) newWidth = Math.floor(relativeX/starWidth)*starWidth + starWidth;
                            else newWidth = relativeX;

                            /* Remplissage des étoiles en jaune.
                             * Si l'utilisateur est sur une note intermédiaire, on arrondie au demi-point le plus près. Cela permet de remplir les étoiles par moitié au minimum (et non par pixel).
                             * Par exemple : 2,3 on arrondi à 2,5. On affiche alors 2,5 étoiles et non 2,3 étoiles */
                            var rate = getNote(newWidth);
                            newWidth = starWidth * rate;
                            average.width(newWidth);

                            if(opts.showRateInfo)
                            {
                                $("p.jRatingInfos")
                                    .css({
                                        left: (e.pageX + opts.rateInfosX)
                                    })
                                    .html(rateInfo(rate, opts.rateMax));
                            }
                        },
                        mouseleave : function(){
                            $("p.jRatingInfos").remove();
                        },
                        click : function(e){
                            var element = this;

                            /*set vars*/
                            hasRated = true;
                            globalWidth = newWidth;
                            nbOfRates--;

                            if(!opts.canRateInfinite && (!opts.canRateAgain || parseInt(nbOfRates) <= 0)) $(this).unbind().css('cursor','default').addClass('jDisabled');

                            if (opts.showRateInfo) $("p.jRatingInfos").fadeOut('fast',function(){$(this).remove();});
                            e.preventDefault();

                            /* Remplissage des étoiles en jaune */
                            var rate = getNote(newWidth);
                            newWidth = starWidth * rate;
                            average.width(newWidth);

                            /* On assigne la valeur choisie par l'utilisateur à l'input qui doit recevoir cette valeur */
                            $("#"+opts.inputID).val(rate);
                        }
                    });

                function getNote(relativeX) {
                    var noteBrut = parseFloat((relativeX*100/widthRatingContainer)*opts.rateMax/100);
                    switch(opts.decimalLength)
                    {
                        // Arrondi au demi-point près
                        case -1 :
                            var note = Math.round((noteBrut+0.1)*2)/2;
                            if(note < opts.rateMinSelect)
                                note = opts.rateMinSelect;
                            break;
                        case 1 :
                            var note = Math.round(noteBrut*10)/10;
                            break;
                        case 2 :
                            var note = Math.round(noteBrut*100)/100;
                            break;
                        case 3 :
                            var note = Math.round(noteBrut*1000)/1000;
                            break;
                        default :
                            var note = Math.round(noteBrut*1)/1;
                    }
                    return note;
                };

                function getStarWidth(){
                    switch(opts.type) {
                        case 'small' :
                            starWidth = 12;
                            starHeight = 10;
                            bgPath = opts.smallStarsPath;
                            break;
                        default :
                            starWidth = 23;
                            starHeight = 20;
                            bgPath = opts.bigStarsPath;
                    }
                };

                function findRealLeft(obj) {
                    if( !obj ) return 0;
                    return obj.offsetLeft + findRealLeft( obj.offsetParent );
                };
            });

    }
})(jQuery);