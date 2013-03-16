  $(document).ready(function () {
	  
	  $( "#author_name" ).autocomplete({
		  appendTo: "#autocomplete",
		  source: "/auteurs/search",
		  minLength: 2,
		  select: function( event, ui ) {
			  if (ui.item)
				  $( "#citation_author_id" ).attr('value',  ui.item.id);
		  }
	  });
	  
	  $( "#author_search" ).autocomplete({
		  source: "/auteurs/search",
		  minLength: 2,
		  select: function( event, ui ) {
			  if (ui.item)
				  document.location = "/a/"+ui.item.id;
		  }
	  });
	  
  });
  
