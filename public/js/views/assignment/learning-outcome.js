$(document).ready(function(){
    // To show the user how many chars left each time they
    // enter text into the text area.
    $('.report').live('keyup paste drop', function(e) { 
        chars = $(this).val();
        totalChars = chars.length;
        newLines = chars.match(/\r|\n/g); // searches for "\r" and "\n" chars.
        newLines = newLines ? newLines.length : 0;
           
        totalChars = totalChars - newLines;
        alert(totalChars);
   
        //id = $(this).attr('id');

        //minLen = $('#minLen-' + id).text();
        //charsLeft = minLen - totalChars;
        //if (charsLeft < 0) {
        //   charsLeft = 0;
        //}
        //
        //$('#charsLeft-' + id).text(charsLeft);
                
    });

    // When page loads, trigger the key up event on .answerText so that chars left will show up
    // accurately after page loads from validation errors.
    //$('.report').keyup();
});