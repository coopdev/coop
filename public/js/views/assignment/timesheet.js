$(document).ready(function() {

   fallElems = $("[semester=fall]");
   fallTotalHrsElem = $("#static_tasks-fallTotalHrs");
   addTotalHrs(fallElems, fallTotalHrsElem);
   
   springElems = $("[semester=spring]");
   springTotalHrsElem = $("#static_tasks-springTotalHrs");
   addTotalHrs(springElems, springTotalHrsElem);

   summerElems = $("[semester=summer]");
   summerTotalHrsElem = $("#static_tasks-summerTotalHrs");
   addTotalHrs(summerElems, summerTotalHrsElem);

   $("[semester=fall]").live('focus', function() {
      setInterval(function() {
         addTotalHrs(fallElems, fallTotalHrsElem);
      }, 500);
   });
   
   
   $("[semester=spring]").live('focus', function() {
      setInterval(function() {
         addTotalHrs(springElems, springTotalHrsElem);
      }, 500);
   });
   
   $("[semester=summer]").live('focus', function() {
      setInterval(function() {
         addTotalHrs(summerElems, summerTotalHrsElem);
      }, 500);
   });
   
});

function addTotalHrs(semesterElems, totalHrField) {
   totalHrs = 0;
   semesterElems.each(function() {
      val = parseFloat($(this).val());
      if (!isNaN(val)) {
         totalHrs += val;
      }

      //alert(fallTotalHrs);
   });

   totalHrField.val(totalHrs);
}



