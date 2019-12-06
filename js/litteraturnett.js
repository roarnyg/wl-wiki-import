(function() {

   // Creates popups for the images
   function _displayImageInfo (){
        $(".author-page-image>img,.author-page-image>.ic-expand").click(function(){
            $.magnificPopup.open({
                mainClass: 'mfp-fade',
                removalDelay: 500,
                items: {
                  src: '#authorImageInfo',
                  type: 'inline',
                  midClick: true
                }
            });
        });
    }


    // THis annotates all images *except* the main author image
    function _addIconToImageInfo() {
        jQuery(".ic-expand").clone().insertAfter(".thumbinner .thumbcaption p");
        jQuery(".thumbinner").after("<div class='image-info-popup white-popup mfp-with-anim mfp-hide img-box-info'></div>");

        jQuery(".img-box-info").each(function() {
            var crr_this = jQuery(this);
            var myString = jQuery(this).parent().find('.thumbimage').attr("src");
            var parts = myString.split('/');
            var aw_filename = parts[parts.length - 2];
            var aw_filename = decodeURIComponent(aw_filename);
            var wikiApiUrl = jQuery("#authorImageInfo").data("wiki-url");
            if (aw_filename) {
                var wiki_api_uri = wikiApiUrl + '?action=query&prop=imageinfo&iiprop=extmetadata&format=json&titles=' + encodeURIComponent('Fil:' + aw_filename);
                var wiki_api_uri2 = wikiApiUrl + '?action=query&prop=imageinfo&iiprop=url&format=json&titles=' + encodeURIComponent('Fil:' + aw_filename);
                var itemurl = jQuery(this).parent().find('.thumbimage').attr("src");

                jQuery.ajax({
                    type: "GET",
                    dataType: "jsonp",
                    url: wiki_api_uri2,
                    success: function(data) {
                        if (data !== undefined && data.query.pages !== undefined) {
                            for (var key in data.query.pages) {
                                var element = data.query.pages[key];
                                if (element.imageinfo !== undefined && element.imageinfo.length > 0) {

                                    itemurl = element.imageinfo[0].url;
                                }
                            }
                        }
                    }
                })

                jQuery.ajax({
                    type: "GET",
                    dataType: "jsonp",
                    url: wiki_api_uri,
                    success: function(data) {
                        if (data !== undefined && data.query.pages !== undefined) {
                            for (var key in data.query.pages) {
                                var element = data.query.pages[key];
                                if (element.imageinfo !== undefined && element.imageinfo.length > 0 && element.imageinfo[0].extmetadata !== undefined) {
                                    var itemExtraMetaData = element.imageinfo[0].extmetadata;
                                    var itemComment = element.imageinfo[0].comment;
                                    var imageinfoHtml = "<div><img src='" + itemurl + "'><div class='image-info-artist'>";

                                    var imageInfos = [];

                                    if (itemExtraMetaData.Artist !== undefined) {

                                        imageInfos.push("<strong>Opphavsperson:</strong> " + itemExtraMetaData.Artist.value);
                                    }
                                    if (itemComment !== undefined) {
                                        imageInfos.push(itemComment);
                                    }

                                    if (itemExtraMetaData.Credit !== undefined) {
                                        imageInfos.push(itemExtraMetaData.Credit.value);
                                    }

                                    if (itemExtraMetaData.ImageDescription !== undefined) {
                                        imageInfos.push(itemExtraMetaData.ImageDescription.value);
                                    }

                                    if (itemExtraMetaData.LicenseShortName !== undefined) {
                                        imageInfos.push(itemExtraMetaData.LicenseShortName.value);
                                    }

                                    if (itemExtraMetaData.LicenseUrl !== undefined) {
                                        imageInfos.push(itemExtraMetaData.LicenseUrl.value);
                                    }

                                    imageinfoHtml += imageInfos.join("<br/>") + "</div>";

                                    if (itemExtraMetaData.Permission !== undefined) {
                                        imageinfoHtml += "<div class='image-info-permission'>" + itemExtraMetaData.Permission.value + "</div>";
                                    }
                                    crr_this.html(imageinfoHtml);
                                }
                            }
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            } //End if

        })


        jQuery(".author-page-content .thumbimage").click(function() {
            var crr = jQuery(this).parent().parent().find('.img-box-info');
            jQuery.magnificPopup.open({
                mainClass: 'mfp-fade',
                removalDelay: 500,
                items: {
                    src: crr,
                    type: 'inline',
                    type: 'inline',
                    midClick: true
                }
            });
        });

        //,.thumbinner .ic-expand
        jQuery(".author-page-content .thumbinner .ic-expand").click(function() {
            var crr = jQuery(this).parent().parent().parent().find('.img-box-info');
            jQuery.magnificPopup.open({
                mainClass: 'mfp-fade',
                removalDelay: 500,
                items: {
                    src: crr,
                    type: 'inline',
                    type: 'inline',
                    midClick: true
                }
            });
        });
    }



    // ADds image information about the main author image to the 'popup' image
    function _getImageInfo() {
        $("#authorImageInfo").each(function() {
            var dataImageName = $(this).data("image-name");
            var wikiApiUrl = $(this).data("wiki-url");
            if (dataImageName) {
                dataImageName = encodeURIComponent(dataImageName);
                var wiki_api_uri = wikiApiUrl + '?action=query&prop=imageinfo&iiprop=extmetadata|comment&format=json&titles=Fil:' + dataImageName;
                $.ajax({
                    type: "GET",
                    dataType: "jsonp",
                    url: wiki_api_uri,
                    success: function(data) {
                        if (data !== undefined && data.query.pages !== undefined) {
                            for (var key in data.query.pages) {
                                var element = data.query.pages[key];
                                if (element.imageinfo !== undefined && element.imageinfo.length > 0 && element.imageinfo[0].extmetadata !== undefined) {
                                    var itemExtraMetaData = element.imageinfo[0].extmetadata;
                                    var imageinfoHtml = "<div class='image-info-artist'>";
                                    var itemComment = element.imageinfo[0].comment;
                                    itemComment = itemComment.replace(/(?:\r\n|\r|\n)/g, '<br />');

                                    var imageInfos = [];

                                    if (itemExtraMetaData.Artist !== undefined) {
                                        $(".author-page-image .img-creator").html("Opphavsperson: " + itemExtraMetaData.Artist.value);
                                        imageInfos.push("<strong>Opphavsperson:</strong> " + itemExtraMetaData.Artist.value);
                                    }

                                    if (itemExtraMetaData.Credit !== undefined) {
                                        imageInfos.push("<strong>Kilde:</strong> " + itemExtraMetaData.Credit.value);
                                    }

                                    if (itemExtraMetaData.Artist == undefined || itemExtraMetaData.Credit == undefined) {
                                        if (itemComment !== undefined) {
                                            imageInfos.push(itemComment);
                                        }
                                    }

                                    if (itemExtraMetaData.ImageDescription !== undefined) {
                                        imageInfos.push(itemExtraMetaData.ImageDescription.value);
                                    }

                                    if (itemExtraMetaData.LicenseShortName !== undefined) {
                                        imageInfos.push(itemExtraMetaData.LicenseShortName.value);
                                    }

                                    if (itemExtraMetaData.LicenseUrl !== undefined) {
                                        imageInfos.push(itemExtraMetaData.LicenseUrl.value);
                                    }

                                    imageinfoHtml += imageInfos.join("<br/>") + "</div>";

                                    if (itemExtraMetaData.Permission !== undefined) {
                                        imageinfoHtml += "<div class='image-info-permission'>" + itemExtraMetaData.Permission.value + "</div>";
                                    }

                                    $("#imageInfoContent").html(imageinfoHtml);
                                }
                            }
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }
        });
    };

    /* Seems to  linkify any literal URLs in the author details box IOK 2019-12-06*/
    function _addLinkToInfoBox() {
        $('.author-page-detail').each(function() {
            // Get the content
            var str = $(this).html();
            // Set the regex string
            var regex = /(https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\/_\.]*(\?\S+)?)?)?)/ig
            // Replace plain text links by hyperlinks
            var replaced_text = str.replace(regex, "<a href='$1' target='_blank'>$1</a>");
            // Echo link
            $(this).html(replaced_text);
        });
    };


    jQuery(document).ready(function() {
        _addLinkToInfoBox();
        _addIconToImageInfo();
        _getImageInfo();
    })


})();
