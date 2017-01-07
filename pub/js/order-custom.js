require([
    'jquery',
    'jquery/ui',
    'MutationObserver'
], function($){
    "use strict";
    $(document).ready(function(){
        function setBackground() {
            var text = '';
            $('.sales-order-index .data-grid-draggable .data-row').each(function (index) {
                $(this).find('.data-grid-cell-content').each(function (i, element) {
                    text = $(element).text();
                    if (text == 'Pending') {
                        $(element).parent().css('background', 'red');
                        return false;
                    } else if (text == 'Canceled') {
                        $(element).parent().css('background', 'gray');
                        return false;
                    }
                });

            });
        }

        setTimeout(function(){
            setBackground();
        }, 3000);
    })
});

