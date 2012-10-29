//$(document).ready(function() {

// Gets the record information of the selected record (username, class, semester) to
// use in queries for different assignment information.
function getRec() {
   // If the search results table (the table with student's name, class, semester, 
   // coordinator, etc.) is present, then the record information should be coming from
   // the selected record, so reset the stored record (#stored-rec) to that. 
   if ( $("#searchresults-table").length > 0 ) {
      $("#stored-rec").data("rec", "");
      rec = $("input[name=rec-choice]:checked").data('rec');
      $("#stored-rec").data("rec", rec);
   } 
   data = $("#stored-rec").data("rec");
   return data[0];
}
        
        
//});
