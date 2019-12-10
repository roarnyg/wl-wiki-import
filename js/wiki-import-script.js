(function( $ ){	
	var importPageSize = 5;
	var importData = [];
	var totalItem = 0 ;
	var currentItemIndex = 0;
	function searchByCategory(obj,searchStr){
		var wiki_api_uri = obj.attr('wiki-api-uri') + '?action=query&list=categorymembers&cmtype=page%7Csubcat&cmprop=ids%7Ctitle%7Ctype&cmlimit=500&format=json&cmtitle=' + encodeURIComponent('Kategori:' + searchStr);
		$.ajax({
			type: "GET",
			dataType: "jsonp",				
			url: wiki_api_uri,
			success: function(response){
				var data = response;
				if(data !== undefined && data.query.categorymembers !== undefined){										
					for(var key in  data.query.categorymembers){
						var element =  data.query.categorymembers[key];
						var pageid = element.pageid;
						var title = element.title;
						if(element.type =='subcat'){
							searchByCategoryId(obj,element.pageid);
						}
						else if(element.type == 'page'){
							var html = '<input class="wiki-pageids" type="checkbox" name="pages[]" pageid="'+pageid+'" value=""> ' + title +'<br>';
							$('.wiki-api-search-results').append(html);	
						}
						
					}
					$('.wiki-api-import-action').show();
				}
				else{
					alert('Can not find any page!');
				}
			}
		});
	}

	function searchByCategoryId(obj,categoryId){
		var wiki_api_uri = obj.attr('wiki-api-uri') + '?action=query&list=categorymembers&cmprop=ids%7Ctitle%7Ctype&cmtype=page%7Csubcat&cmlimit=500&format=json&cmpageid=' + categoryId;
		
		$.ajax({
			type: "GET",
			dataType: "jsonp",				
			url: wiki_api_uri,
			success: function(response){
				var data = response;

				if(data !== undefined && data.query.categorymembers !== undefined){
					
					for(var key in  data.query.categorymembers){
						var element =  data.query.categorymembers[key];
						var pageid = element.pageid;
						var title = element.title;
						if(element.type =='subcat'){
							searchByCategoryId(obj,element.pageid);
						}
						else if(element.type == 'page'){
							var html = '<input class="wiki-pageids" type="checkbox" name="pages[]" pageid="'+pageid+'" value=""> ' + title +'<br>';
							$('.wiki-api-search-results').append(html);	
						}
						
					}
					$('.wiki-api-import-action').show();
				}
				else{
					alert('Can not find any page!');
				}
			}
		});
	}

	function searchByName(obj,searchStr){
		var wiki_api_uri = obj.attr('wiki-api-uri') + '?action=query&generator=search&gsrlimit=max&prop=info&format=json&gsrsearch=' + encodeURIComponent(searchStr);
		$.ajax({
			type: "GET",
			dataType: "jsonp",				
			url: wiki_api_uri,
			success: function(response){
				var data = response;

				if(data !== undefined && data.query.pages !== undefined){
					for(var key in  data.query.pages){
						var element = data.query.pages[key];
						var pageid = key;
						var title = element.title;
						var html = '<input class="wiki-pageids" type="checkbox" name="pages[]" pageid="'+pageid+'" value=""> ' + title +'<br>';
						$('.wiki-api-search-results').append(html);
					}
					$('.wiki-api-import-action').show();
				}
			}
		});
	}
	function advanceSearch(obj,category1, category2, condition){
		var category1SearchString = category1;
		var category2SearchString = category2;
		var completeSearchStr =	"";
		var wiki_api_search_sub_cat_1 = obj.attr('wiki-api-uri') + '?action=query&list=categorymembers&cmtype=subcat&cmprop=title&cmlimit=500&format=json&cmtitle=' + encodeURIComponent('Kategori:' + category1);
		$.ajax({
			type: "GET",
			dataType: "jsonp",				
			url: wiki_api_search_sub_cat_1,
			success: function(data){
				if(data !== undefined && data.query.categorymembers !== undefined){					
					for(var key in  data.query.categorymembers){
						var element =  data.query.categorymembers[key];
						var title = element.title.replace("Kategori:","");
						category1SearchString +="|"+title;
					}
				}
						console.log(category1SearchString);
				var wiki_api_search_sub_cat_2 = obj.attr('wiki-api-uri') + '?action=query&list=categorymembers&cmtype=subcat&cmprop=title&cmlimit=500&format=json&cmtitle=' + encodeURIComponent('Kategori:' + category2);
				$.ajax({
					type: "GET",
					dataType: "jsonp",				
					url: wiki_api_search_sub_cat_2,
					success: function(data){
						if(data !== undefined && data.query.categorymembers !== undefined){					
							for(var key in  data.query.categorymembers){
								var element =  data.query.categorymembers[key];
								var title = element.title.replace("Kategori:","");
								category2SearchString +="|"+title;
							}
						}	
						console.log(category2SearchString);					
						switch(condition){
							case "And": searchStr ='incategory:"'+category1SearchString+'" incategory:"'+category2SearchString+'"'; break;	
							case "Or": searchStr ='incategory:"'+category1SearchString+'|'+category2SearchString+'"'; break;
							case "Not in": searchStr ='incategory:"'+category1SearchString+'"-incategory:"'+category2SearchString+'"';; break;
						}
						var wiki_api_uri = obj.attr('wiki-api-uri') + '?action=query&generator=search&gsrlimit=max&format=json&gsrsearch=' + encodeURIComponent(searchStr);
						$.ajax({
							type: "GET",
							dataType: "jsonp",				
							url: wiki_api_uri,
							success: function(data){
								if(data !== undefined && data.query.pages !== undefined){
									for(var key in  data.query.pages){
										var element = data.query.pages[key];
										var pageid = key;
										var title = element.title;
										var html = '<input class="wiki-pageids" type="checkbox" name="pages[]" pageid="'+pageid+'" value=""> ' + title +'<br>';
										$('.wiki-api-search-results').append(html);
									}
									$('.wiki-api-import-action').show();
								}
								else{
									alert('Can not find any page!');
								}
							}
						});
					}
				});		
			}
		});
	}
	function importSiteData(){
		var dataToImport = [];
		if(importData.length>0){
			dataToImport = importData.splice(0,importPageSize);
		}else{
			dataToImport = importData;
		}
		currentItemIndex += dataToImport.length;
                console.log("In importSiteData");
		$("#importLoading").fadeIn();
		$("#importLoading span").text(currentItemIndex+"/"+totalItem);

console.log("ajaxurl is " + ajaxurl);

		$.ajax({
	        url: ajaxurl,
	        data: {
	            'action':'wiki_api_import',
	            'data' : dataToImport
	        },
	        success:function(dataStr) {
	            // This outputs the result of the ajax request
	           
	            if(importData.length>0){
	            	importSiteData();
	            }else{
					$("#importLoading").fadeOut();
	            	alert("Import has been successfully finished!");
	            }
	        },
	        error: function(errorThrown){
	            console.log(errorThrown);
				$("#importLoading").fadeOut();
	        }
	    });  
	}
	

	$( document ).ready( function() {
		$('.wiki-api-import-action').hide();
		$('#wiki-api-import-button').click(function(){
                        console.log("Importing...");
			currentItemIndex = 0;
			importData = [];
			// This does the ajax request
			$('.wiki-pageids').each(function(index,value){
				if($(value).is(':checked')){
					importData.push($(value).attr('pageid'));	
				}
				
			});
			totalItem = importData.length;
                        console.log("To import %j ", importData);
			importSiteData();	    

		});
		$('#wiki-api-import-select-all').click(function(){
			$('.wiki-pageids').each(function(index,value){
				var crtChecked = $(value).prop('checked');
				$(value).prop('checked', !crtChecked);				
			});
		});
		$("#wiki-api-import-advance-search").click(function(){
			$(this).toggleClass("button-primary");
			$("#wikiAdvanceSearchField").toggle();
			$("#wikiSearchForm input[type=checkbox]").prop("checked",false);
		});
		$("#wikiSearchForm input[type=checkbox]").change(function(){			
			$("#wiki-api-import-advance-search").removeClass("button-primary");
			$("#wikiAdvanceSearchField").hide();
		});

		$('#wiki-api-search-button').click(function(){		
			var wiki_api_key_word = $('#wikiApiSearchText').val().trim();
			$('.wiki-api-import-action').hide();
			$('.wiki-api-search-results').html('');
			if($("#wiki-api-import-advance-search").hasClass("button-primary")){				
				var wiki_api_extra_key_word = $('#wikiApiSearchExtraText').val().trim();	
				var wiki_api_condition = $('#wikiAdvanceSearchCondition').val();
				if((wiki_api_key_word != '')&&(wiki_api_extra_key_word!='')){
					advanceSearch($(this),wiki_api_key_word,wiki_api_extra_key_word,wiki_api_condition);
				}
			}else{				
				if(wiki_api_key_word != ''){					
					if($("#search-type-name").is(':checked')){
						searchByName($(this),wiki_api_key_word);
					}
					if($("#search-type-category").is(':checked')){
						searchByCategory($(this),wiki_api_key_word);
					}

				}
			}
		})


	});
})(jQuery);
console.log("Loaded admin script");
