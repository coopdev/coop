$(document).ready(function(){
    $('.answerText').live('keyup paste drop', function(e) { 
        chars = $(this).val();
        totalChars = chars.length;
        newLines = chars.match(/\r|\n/g); // searches for "\r" and "\n" chars.
        newLines = newLines ? newLines.length : 0;
           
        totalChars = totalChars - newLines;
   
        id = $(this).attr('id');

        minLen = $('#minLen-' + id).text();
        charsLeft = minLen - totalChars;
        if (charsLeft < 0) {
           charsLeft = 0;
        }
        
        $('#charsLeft-' + id).text(charsLeft);
                
    });

    // When page loads, trigger the key up event so that chars left will show up
    // accurately after page loads from validation errors.
    $('.answerText').keyup();
});
