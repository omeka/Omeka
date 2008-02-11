if(typeof Omeka == 'undefined') {
	Omeka = {};
}

if(typeof Omeka.Search == 'undefined') {
	Omeka.Search = {};
}

Object.extend(Omeka.Search, {
	
	activateSearchButtons: function() {
		var addButton = document.getElementsByClassName('add_search');
	
		var removeButtons = document.getElementsByClassName('remove_search');
	
		var addAdvancedSearch = function() {
			//Copy the div that is already on the search form
			var oldDiv = $$('.search-entry').last();
	
			//Clone the div and append it to the form
			var div = oldDiv.cloneNode(true);
					
			oldDiv.up().appendChild(div);
	
			var inputs = $A(div.getElementsByTagName('input'));
			var selects = $A(div.getElementsByTagName('select'));
	
			//Find the index of the last advanced search formlet and inc it
			//I.e. if there are two entries on the form, they should be named advanced[0], advanced[1], etc
			var inputName = inputs[0].getAttribute('name');
	
			//Match the index, parse into integer, increment and convert to string again				
			var index = inputName.match(/advanced\[(\d+)\]/)[1];
			var newIndex = (parseInt(index) + 1).toString();
	
			//Reset the selects and inputs	
					
			inputs.each(function(i) {
				i.value = '';
				i.setAttribute('name', i.name.gsub(/\d+/, newIndex) );
			});
			selects.each(function(s) {
				s.selectedIndex = 0;
				s.setAttribute('name', s.name.gsub(/\d+/, newIndex) );
			});	
									
			//Make the button special again
			var add = div.getElementsByClassName('add_search').first();
	
			add.onclick = addAdvancedSearch;
	
			var remove = div.getElementsByClassName('remove_search').first();
			removeAdvancedSearch(remove);
		}
	
		var removeAdvancedSearch = function(button) {
			Event.observe(button, 'click', function() {
					button.up().destroy();
			});
		}
	
		//Make each button respond to clicks
		addButton.each(function(button) {
			Event.observe(button, 'click', addAdvancedSearch);
		});
	
		removeButtons.each(function(button) {
			removeAdvancedSearch(button);
		});
	},

	//Hey we want to be able to toggle the advanced search
	toggleSearch: function() {

		var basicForm = $('basic_search');
		var advancedForm = $('advanced_search');

		var disableAndHide = function(form) {
			new Effect.BlindUp(form);
		//	form.hide();
			form.getElementsBySelector('input, select').invoke('disable');
			$('advanced_search_header').innerHTML = 'Show Advanced Options';
		}
	
		var enableAndShow = function(form) {
			new Effect.BlindDown(form);
	//		form.show();
			form.getElementsBySelector('input, select').invoke('enable');
			$('advanced_search_header').innerHTML = 'Hide Advanced Options';
		}
	
		var toggleAdvancedForm = function() {
			if(advancedForm.visible()) {
				disableAndHide(advancedForm);
			}else {
				enableAndShow(advancedForm);
			}
		}
	
		//Show the advanced form
		Event.observe('advanced_search_header', 'click', toggleAdvancedForm);
		
		//Hide the advanced form by default
		advancedForm.getElementsBySelector('input, select').invoke('disable');
		advancedForm.hide();
	},

});