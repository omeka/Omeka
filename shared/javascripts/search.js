if(typeof Omeka == 'undefined') {
	Omeka = {};
}

Omeka.Search = {};

Object.extend(Omeka.Search, {
	activateSearchButtons: function() {
		var addButton = $$('.add_search');
	
		var removeButtons = $$('.remove_search');
	
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
			var add = div.select('.add_search').first();
	
			add.onclick = addAdvancedSearch;
	
			var remove = div.select('.remove_search').first();
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
	}

});