$(document).ready(function(){

    // To show the user how many chars left  every .5 seconds. the setInterval function
    // will call the countChars function every .5 seconds on the text area that is being
    // focused.
    $('.answerText').live('focus', function() {
       answerText = $(this);
       setInterval(function() {
          countChars(answerText);
       }, 3000);
    });

    // Bind my custom 'countChars' event to all answerTexts so it can be triggered on 
    // page load.
    $('.answerText').live('countChars', function(e) { 
       countChars($(this));
    });


    // When page loads, trigger my custom 'countChars' event on .answerText so that chars left will show up
    // accurately after page loads from validation errors.
    $('.answerText').trigger('countChars');
});


/* FUNCTIONS */

function countChars(answerText)
{
     chars = answerText.val();
     totalChars = chars.length;
     newLines = chars.match(/\r|\n/g); // searches for "\r" and "\n" chars.
     newLines = newLines ? newLines.length : 0;
        
     totalChars = totalChars - newLines;

     id = answerText.attr('id');

     minLen = $('#minLen-' + id).text();
     alert("" + minLen);
     charsLeft = minLen - totalChars;
     if (charsLeft < 0) {
        charsLeft = 0;
     }
     
     $('#charsLeft-' + id).text(charsLeft);
}
