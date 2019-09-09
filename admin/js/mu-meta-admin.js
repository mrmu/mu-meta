(function( $ ) {
	'use strict';

	$(function(){
		$( ".mm_datepicker" ).datepicker();

        //Tabs functionality
        //Firstly hide all content divs
        $('.generic-tabs div.tab-content').hide();
        //Then show the first content div
        $('.generic-tabs div.tab-content:first').show();
        //Add active class to the first tab link
        $('.generic-tabs ul.tabs li:first').addClass('active');
        //Functionality when a tab is clicked
        $('.generic-tabs ul.tabs li a').on('click', function(){
        	//Firstly remove the current active class
            $('.generic-tabs ul.tabs li').removeClass('active');
            //Apply active class to the parent(li) of the link tag
            $(this).parent().addClass('active');
            //Set currentTab to this link
            var currentTab = $(this).attr('href');
            //Hide away all tabs
            $('.generic-tabs div.tab-content').hide();            
            //show the current tab
            $(currentTab).show();
            //Stop default link action from happening
            return false;
        });	
	});
	
})( jQuery );
