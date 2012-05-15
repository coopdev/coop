$('document').ready(function() {
   $('.classTab').live('click', function(e) {
      e.preventDefault();
      val = $(this).attr('value');
      alert(val);

   });


});

