  $(document).ready(function () {
	  
	  $( "#author_name" ).autocomplete({
		  appendTo: "#autocomplete",
		  source: "/auteurs/search",
		  minLength: 2,
		  select: function( event, ui ) {
			  console.log( ui.item ?
			  "Selected: " + ui.item.value + " id " + ui.item.id :
			  "Nothing selected, input was " + this.value );
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
  
