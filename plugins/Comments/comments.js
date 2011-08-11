function Comments()
{
}


Comments.prototype = 
{
	_item_id : null,
		
	show : function()
	{
		this._item_id = window.document.location.href.replace(/.+items\/show\//,"");
		this._sendAJAXRequest("/plugins/Comments/service.php", {
			data : {'act' : 'get_commets', 'item_id' : this._item_id}
			, success : function(res){this._showCommets(res);}.bindWith(this)
			, error : function(res){this._errorHandler(res);}.bindWith(this)
		});
	},
	
	
	save : function()
	{
		var rate = document.getElementById('comment_rating');
		var body = document.getElementById('new_comment_body');
		this._sendAJAXRequest("/plugins/Comments/service.php", {
			data : {'act' : 'save_commet', 'item_id' : this._item_id, 'rate' : rate.value, 'description' : body.value}
			, success : function(res){
				if (this._showCommets(res))
				{
					rate.value = '';
					body.value = '';
				}
			}.bindWith(this)
			, error : function(res){this._errorHandler(res);}.bindWith(this)
		});
	},
	
	
	_getComment : function(data)
	{
		var mainDiv = document.createElement('div');
		mainDiv.className = 'main';
		var authorDiv = document.createElement('div');
		authorDiv.innerHTML = data['added'] + '&nbsp;&nbsp;' + data['guest_name'];
		authorDiv.className = 'author';
		mainDiv.appendChild(authorDiv);
		var bodyDiv = document.createElement('div');
		bodyDiv.innerHTML = data['description'].replace(/\n/g, '<br>');
		bodyDiv.className = 'comment_body';
		mainDiv.appendChild(bodyDiv);
		return mainDiv;
	},
	
	_showCommets : function(res)
	{
		if (res['code'] != 0)
		{
			alert(res['message']);
			return false;
		}
		var d = document.getElementById('CommentsPost');
		for (var i=0; i<res['data'].length; i++)
			d.appendChild(this._getComment(res['data'][i]));
		d.style.display = 'block';
		if (res['rate'])
		{
			var r = document.getElementById('ratingValue');
			r.innerHTML = res['rate'];
		}
		return true;
	},
	
	_errorHandler : function(res)
	{
		if (res['message'])
			alert(res['message']);
	},

	_sendAJAXRequest : function(url, data)
	{
		jdata = new Object();
		jdata.type = data.type != undefined ?data.type:"POST";
		jdata.url = url;
		jdata.data = data.data != undefined ?data.data:{};
		jdata.dataType = 'json';
		jdata.async = data.async != undefined ?data.async:true;
		if (jdata.async)
		{
			jdata.error = function(res)
			{
				if (data.error)
					data.error(res.responseText);
			}; 
			jdata.success = function(res)
			{
				if (data.success)
					data.success(res);
			};
		    jQuery.ajax(jdata);
		}
		else
		{
			var res = this._evalJSON(jQuery.ajax(jdata).responseText);
			if (data.success)
				data.success(res);
		}
	},
	
	getRates : function(items)
	{
		this._sendAJAXRequest("/plugins/Comments/service.php", {
			data : {'act' : 'get_rates', 'item_ids' : items}
			, success : function(res){this._showRates(res);}.bindWith(this)
			, error : function(res){this._errorHandler(res);}.bindWith(this)
		});
	},
	
	_showRates : function(res)
	{
		for(item in res['data'])
		{
			if (res['data'].hasOwnProperty(item))
			{
				document.getElementById('ratingValue_'+item).innerHTML = res['data'][item];
			}
		}
	},

	_evalJSON : function(src)
	{
	    if (typeof(JSON) == 'object' && JSON.parse)
	        return JSON.parse(src);
	    return eval("(" + src + ")");
	}
};


Function.prototype.bindWith = function(bindWith, passArgs)
{
	var fn = this;
	return function()
	{
		return fn.apply(bindWith, passArgs ? passArgs : arguments);
	};
};


if (window['OMEKA'] == undefined)
	OMEKA = {};
OMEKA.COMMENTS = new Comments();
