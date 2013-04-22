function updateWallpaper() {
	$("figure").addClass("load");
    url_to_load = '/wallpaper/' +
    $('#author').val() + '.' + 
    $('#citation').val() + '.' + 
    $('#width').val() + '.' + 
    $('#height').val() + '.' + 
    $('#bgcolor').val().replace("#", "") + '.' + 
    $('#textcolor').val().replace("#", "") + '.' ;
	url_to_load += $("#authorname").is(':checked')?"1.":"0.";
	url_to_load += $("#authoravatar").is(':checked')?"1":"0";
	url_to_load += (background && xhr_object)?"."+xhr_object.responseText:"";
	url_to_load += '.png';
	$("#wallpaper").attr("src", url_to_load).load(function() {
		$("figure").removeClass("load");
	});
    $(".action a:first-child").attr("href", url_to_load);
    return false;
}
var background = false;
var xhr_object = false;


	$(document).ready(function () {
	  
	  $(".action .note").on("click", function () {
		  $(this).find("span+span").load("/note/citation/"+$(this).data("citation"));
	  });
	  
	  $(".action a").on("click", function () {
		  $(this).addClass("actioned");
	  });
	  
	  if ($("#author_name").length) {
		  $("#author_name").autocomplete({
			  appendTo: "#autocomplete",
			  source: "/auteurs/search",
			  minLength: 2,
			  select: function( event, ui ) {
				  if (ui.item)
					  $( "#citation_author_id" ).attr('value',  ui.item.id);
			  }
		  });
	  }

	  if ($("#author_search").length) {
		  $("#author_search").autocomplete({
			  source: "/auteurs/search",
			  minLength: 2,
			  select: function( event, ui ) {
				  if (ui.item)
					  document.location = "/a/"+ui.item.id;
			  }
		  });
	  }
	  
	  if ($(".checkbox").length) {
		  $(".checkbox").uniform();  
	  }
	  
	  $("#wallpaper-form input").change(function () { return updateWallpaper(); });
	  
	  Dropzone.options.dropzone = {
		dictDefaultMessage: 'Image à mettre en fond (Glissez / Déposez)',
	    init: function() {
		      this.on("complete", function(file) {
		    	  background = true;
		    	  updateWallpaper();
		      });
		      this.on("sending", function(file, xhr, formData) {
		    	  xhr_object = xhr;
		    	  $("figure").addClass("load");
		      });
	  }};
	  
  });
  
