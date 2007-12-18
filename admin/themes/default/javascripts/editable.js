/*
var EditableField = Class.create();

EditableField.prototype = {
	initialize: function(div, fieldName, ajaxUri, recordId, fieldType) {
		this.div = div;
		this.fieldName = fieldName;
		this.ajaxUri = ajaxUri;
		this.recordId = recordId;
		this.fieldType = fieldType;
		this.boundMakeEditable = this.makeEditable.bindAsEventListener(this);		
		Event.observe(this.div, "click", this.boundMakeEditable);
	},
	
	escapeSomeHtml: function(str) {
		//This is to make this compatible with h()
		//Right now the allowed tags are hard-coded but that may change
		var html = str.escapeHTML();

		var catchTags = /&lt;\/?(em|b|strong|del|span|cite|blockquote)( .*?)?&gt;/ig;

		var unescapedHtml = html.replace( catchTags, function(htmlStr){
			return htmlStr.unescapeHTML();
		});

		return unescapedHtml;
	},
	
	makeFormElement: function() {
		switch(this.fieldType) {
			case 'text':
				this.formElement = document.createElement("input");
				this.formElement.setAttribute("type", "text");
				this.formElement.setAttribute("class","textinput");
				this.formElement.value = this.text;
			break;
			case 'textarea': 
			default:
				this.formElement = document.createElement("textarea");
				this.formElement.setAttribute("class","textinput");
				this.formElement.setAttribute("rows","10");
				this.formElement.setAttribute("cols","50");
				
				this.formElement.innerHTML = this.text;
			break;
		}		
	},
	
	makeEditable: function() {
		Event.stopObserving(this.div, "click", this.boundMakeEditable); 
				
		this.text = this.div.innerHTML.strip();
		
		this.div.innerHTML = '';
		
		this.editForm = document.createElement("form");	
					
		this.makeFormElement();
		
		this.formElement.setAttribute('name', this.fieldName);
		
		this.editForm.appendChild(this.formElement);
		this.div.appendChild(this.editForm);
		
		//Now add an 'Edit' link
		
		editLink = document.createElement("a");
		editLink.setAttribute("href", "javascript:void(0)");
		editLink.innerHTML = 'Edit';
		this.div.appendChild(editLink);
		Event.observe(editLink, "click", this.sendEdit.bindAsEventListener(this));
		
	},
	
	sendEdit: function() {
		//Remember that noRedirect must be set to true for the ajax to work
		
		var that = this;
		var opt = {
			parameters: "noRedirect=true&"+Form.serialize(this.editForm),
			method: "post",
			onSuccess: function(t, item) {
				that.div.innerHTML = that.escapeSomeHtml(item[that.fieldName]);
				new Effect.Highlight(that.div, {duration:'2.0',startcolor:'#ffff99', endcolor:'#ffffff'})
				Event.observe(that.div, "click", that.boundMakeEditable);
			}
		}
		
		new Ajax.Request(this.ajaxUri + "?id="+this.recordId, opt);
	}
}

var EditableSelect = Class.create();

EditableSelect.prototype = Object.extend({
	//This is duplicated from EditableField with the last parameter changed 
	initialize: function(div, fieldName, ajaxUri, recordId, selectElement) {
		this.div = div;
		this.fieldName = fieldName;
		this.ajaxUri = ajaxUri;
		this.recordId = recordId;
		this.selectElement = selectElement;
		this.boundMakeEditable = this.makeEditable.bindAsEventListener(this);		
		Event.observe(this.div, "click", this.boundMakeEditable);
		
	},

	makeFormElement: function() {
		this.formElement = selectElement;	
	}
}, EditableField.prototype);

//From http://developer.taboca.com/cases/en/client-javascript-dom-parser/
function parseXML(string) {
	// Mozilla and Netscape browsers
    if (document.implementation.createDocument) {
        var parser = new DOMParser()
        doc = parser.parseFromString(string, "text/xml")
    // MSIE
    } else if (window.ActiveXObject) {
        doc = new ActiveXObject("Microsoft.XMLDOM")
        doc.async="false"
        doc.loadXML(string)
    }
    return doc;
} */