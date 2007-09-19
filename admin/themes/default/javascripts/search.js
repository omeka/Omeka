//Show or hide the search form depending on whether the header icon was clicked
function toggleSearch() {
	//alert('foo');
	var search = $('search');
	if(!search) return;
	search.hide();
	var searchHeader = $('search-header');
	if(!searchHeader) return;
	searchHeader.style.cursor = "pointer";
	searchHeader.onclick = function() {
		new Effect.toggle(search,'blind',{duration:0.5});
		searchHeader.toggleClassName('open','close');
		return false;
	}
}

function activateSearchButtons()
{
	var addButton = document.getElementsByClassName('add_search');
	
	//Make each button respond to clicks
	addButton.each(function(button) {
		Event.observe(button, 'click', addAdvancedSearch);
	});
	
	var removeButtons = document.getElementsByClassName('remove_search');
	
	removeButtons.each(function(button) {
		removeAdvancedSearch(button);
	});
}

function removeAdvancedSearch(button) {
	Event.observe(button, 'click', function() {
			button.up().destroy();
	});
}

//Hey we want to be able to toggle between basic and advanced search
function switchBasicAdvancedSearch() {

	var basicForm = $('basic_search');
	var advancedForm = $('advanced_search');
	
	var showAdvancedForm = function() {
		disableAndHide(basicForm);
		enableAndShow(advancedForm);

		//Click it more than once shouldn't do anything
		Event.stopObserving('advanced_search_header', 'click', showAdvancedForm);
		
		//But it should start paying attention again to see if 'basic' is clicked
		Event.observe('basic_search_header', 'click', showBasicForm);
	};
	
	var showBasicForm = function() {
		disableAndHide(advancedForm);
		enableAndShow(basicForm);

		Event.stopObserving('basic_search_header', 'click', showBasicForm);
		Event.observe('advanced_search_header', 'click', showAdvancedForm);
	};
	
	var disableAndHide = function(form) {
		new Effect.BlindUp(form);
		form.hide();
		form.getElementsBySelector('input, select').invoke('disable');
	}
	
	var enableAndShow = function(form) {
		new Effect.BlindDown(form);
		form.show();
		form.getElementsBySelector('input, select').invoke('enable');
	}
	
	//Show the advanced form
	Event.observe('advanced_search_header', 'click', showAdvancedForm);
	
	//Show the basic form
	Event.observe('basic_search_header', 'click', showBasicForm);
	
	
	//Hide the advanced form by default
	disableAndHide(advancedForm);
}

function addAdvancedSearch() {
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
