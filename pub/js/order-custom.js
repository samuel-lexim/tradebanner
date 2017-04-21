require([
    'jquery',
    'jquery/ui'
], function($){
    "use strict";
    $(document).ready(function(){
        var text = '';


        function setBackground() {
            console.log('setBackground');
          
            $('.sales-order-index .data-grid-draggable .data-row .data-grid-cell-content').each(function (index, element) {
                text = $(element).text();
                if (text == 'Order Received') {
                    $(element).css('background-color', '#73a724');
                    $(element).css('color', '#fff');
                  
                } else if (text == 'Printing') {
                    $(element).css('background-color', '#1e73be');
                    $(element).css('color', '#fff');
                  
                } else if (text == 'Pending Payment') {
                    $(element).css('background-color', '#ffba00');
                    $(element).css('color', '#fff');
                  
                } else if (text == 'On Hold') {
                    $(element).css('background-color', '#999999');
                    $(element).css('color', '#fff');
                 
                } else if (text == 'Complete') {
                    $(element).css('background-color', '#000000');
                    $(element).css('color', '#fff');
                  
                } else if (text == 'Canceled') {
                    $(element).css('background-color', '#dd3333');
                    $(element).css('color', '#fff');
                 
                } else if (text == 'Failed') {
                    $(element).css('background-color', '#d0c21f');
                    $(element).css('color', '#fff');
               
                } else if (text == 'Ready for Pickup') {
                    $(element).css('background-color', '#dd9933');
                    $(element).css('color', '#fff');
                   
                } else if (text == 'Shipped') {
                    $(element).css('background-color', '#8224e3');
                    $(element).css('color', '#fff');
              
                } else if (text == 'Local Delivery') {
                    $(element).css('background-color', '#00b3c6');
                    $(element).css('color', '#fff');
               
                } else if (text == 'Need Artwork') {
                    $(element).css('background-color', '#f97cda');
                    $(element).css('color', '#fff');
                
                } else if (text == 'Closed') {
                    $(element).css('background-color', '#9233dd');
                    $(element).css('color', '#fff');
                
                } 
            });
           
        }

        function delayInitBackground() {            
            setTimeout(function(){
                setBackground();              
				$('.action-menu-item:contains(Print)').prop("target", "_blank");				
            }, 3000);
             
        }

        delayInitBackground();

        setTimeout(function(){
            delayInitBackground();
            $('.admin__data-grid-pager-wrap .action-next').click(function(){
                delayInitBackground();
            });

            $('.admin__data-grid-pager-wrap .action-previous').click(function(){
                delayInitBackground();
            });

            $('.admin__data-grid-pager-wrap #pageCurrent').change(function(){
                delayInitBackground();
            });

            $('.admin__data-grid-pager-wrap .selectmenu-value > input').change(function(){
                delayInitBackground();
            });
          
        }, 3000);
       

    })
});

