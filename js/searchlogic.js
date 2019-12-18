(function () {

    var _animatingAdvanceSearch = false;

    var _advanceSearch = function(){
        jQuery("#advanceSearchBut").click(function(){
            if(!_animatingAdvanceSearch){
                _animatingAdvanceSearch = true;
                if(jQuery("#advanceSearchContent").is(":hidden")){
                    jQuery("#advanceSearchContainer").addClass("active");
                    jQuery("#advanceSearchContainer>a .item-text").fadeOut();
                    jQuery("#advanceSearchContent").slideDown(300,function(){
                        _animatingAdvanceSearch = false;
                    });
                }else{
                    jQuery("#advanceSearchContainer>a .item-text").fadeIn();
                    jQuery("#advanceSearchContent").slideUp(300,function(){
                        jQuery("#advanceSearchContainer").removeClass("active");
                        _animatingAdvanceSearch = false;
                    });
                }
            }
        });
        jQuery("#advanceSearchContent p:has(input)").each(function(){
            if(jQuery("input",this).prop("checked")){
                jQuery(this).addClass("checked")
            }
            jQuery(">label",this).click(function(){
                if(jQuery(this).prev("input").prop("checked")){
                    jQuery(this).parent().removeClass("checked")
                }else{
                    jQuery(this).parent().addClass("checked")
                }
            });
        });
        jQuery("#searchsubmit").click(function(e){
            if(!jQuery("#s").val()){
                jQuery("#s").val(" ");
            }
            if(jQuery("#advanceSearchContent").is(":hidden")){
                jQuery("#municipalitySelectSearch").val("");
                jQuery("#advanceSearchContent input[type=checkbox").prop("checked",false);
            }
        });
        jQuery("#checkAllBut").each(function(){
            if(jQuery("#advanceSearchContent>ul input[type=checkbox]").not(":selected").length==0){
                jQuery(this).addClass("selected");
            }
            jQuery(this).click(function(e){
                e.preventDefault();
                if(jQuery(this).hasClass("selected")){
                    jQuery("#advanceSearchContent>ul input[type=checkbox]").prop("checked",false);
                    jQuery("#advanceSearchContent>ul p").has("input[type=checkbox]").removeClass("checked");
                }else{
                    jQuery("#advanceSearchContent>ul input[type=checkbox]").prop("checked",true);
                    jQuery("#advanceSearchContent>ul p").has("input[type=checkbox]").addClass("checked");
                }
                jQuery(this).toggleClass("selected");
            });
        });
        jQuery("#advanceSearchBoxBut").click(function(){
            jQuery("#searchsubmit").click();
        });
    }
    var _styleSelectbox = function(){
        jQuery('select.styled-selectbox').each(function () {
            jQuery(this).wrap("<div class='styled-selectbox-container'></div>");
            var title = jQuery('option:selected', this).text();
            jQuery(this).css({ 'z-index': 10, 'opacity': 0, '-khtml-appearance': 'none' })
                .after('<span class="select">' + title + '<i></i></span>')
                .change(function () {
                    val = jQuery('option:selected', this).text();
                    jQuery(this).next().html(val+"<i></i>");
                });
        });
    }

    var _applyCustomScrollbar = function(){
         jQuery(window).load(function(){
            jQuery(".scroll-box").mCustomScrollbar({theme: "dark"});
        });
    }

    var _municipalitySelectRedirect = function(){
        jQuery('#municipalitySelect').change(function(){
            if(jQuery(this).val()){
                window.location.href= encodeURI("/?s=+&mu[]="+jQuery(this).val());
            }
        });
    }
    var _authorFromRegionSelectRedirect = function(){
        jQuery('#authorFromRegionSelect').change(function(){
            if(jQuery(this).val()){
                window.location.href=jQuery(this).val();
            }
        });
    }

    var _showAdvanceSearchBox = function(){
		if(window.outerWidth < 980) {
			jQuery("#advanceSearchContainer>a .item-text").fadeIn();
			jQuery("#advanceSearchContent").hide();
			jQuery("#advanceSearchContent").slideUp(300,function(){
				jQuery("#advanceSearchContainer").removeClass("active");
				jQuery("#advanceSearchContainer").addClass("activesssss");
				_animatingAdvanceSearch = false;
			});
		}else{
			jQuery("#advanceSearchContainer").addClass("active");
			jQuery("#advanceSearchContent").show();
			jQuery("#advanceSearchBut .item-text").hide();
		}
    }

  jQuery(document).ready(function () {
    _advanceSearch();
    _styleSelectbox();
    _applyCustomScrollbar();
    _municipalitySelectRedirect();
    _authorFromRegionSelectRedirect();
    var advancedargs = decodeURIComponent(window.location.search).match(/[[]/);
    if (jQuery('body.search').length>0 && advancedargs ) {
        _showAdvanceSearchBox();
    }
});


})();
