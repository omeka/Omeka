	var OmekaMap = Class.create();
	
	OmekaMap.prototype = {
		initialize: function(mapDiv, ajaxUri, centerLatitude, centerLongitude, centerZoomLevel, width, height, options) {
			this.mapDiv = $(mapDiv);
			this.ajaxUri = ajaxUri;
			this.centerLatitude = centerLatitude;
			this.centerLongitude = centerLongitude;
			this.centerZoomLevel = centerZoomLevel;
			this.width = width;
			this.height = height;
			this.options = options;
			Event.observe(window,'load', this.makeMap.bindAsEventListener(this));
		},
		
		addPoints: function(t, json) {
			this.points = json;
			//If there is only one point, make it the center
			if(this.points.length == 1) {
				point = this.points[0];
				centerPoint = new GLatLng(point['latitude'], point['longitude']);
				this.mapObj.setCenter(centerPoint);
				marker = new GMarker(centerPoint, this.options);
				this.mapObj.addOverlay(marker);
			}
			//else if there are no points, make the center an overlay
			else if(this.points.length == 0) {
				centerPoint = new GLatLng(this.centerLatitude, this.centerLongitude);
				marker = new GMarker(centerPoint);
				this.mapObj.addOverlay(centerPoint);
			}
			//else add the points from the ajax call
			else {
				for (var i=0; i < this.points.length; i++) {
					point = this.points[i];
					mapPoint = new GLatLng(point['latitude'], point['longitude']);
					marker = new GMarker(mapPoint);
					this.mapObj.addOverlay(marker);
				};
			}
		},
		
		//Make the map
		makeMap: function() {
			if (GBrowserIsCompatible()) {
		 		this.mapDiv.setStyle({width: (this.width+'px'), height: (this.height+'px')});
		      	this.mapObj = new GMap2(this.mapDiv);
				mapCenter = new GLatLng(this.centerLatitude, this.centerLongitude);
		       this.mapObj.setCenter( mapCenter, this.centerZoomLevel);
			   this.mapObj.addControl(new GSmallMapControl());

				var _this = this;
				opt = {
					onComplete: function(t, json) {
						_this.addPoints(t, json);
					},
					method:'post', 
					asynchronous:true
				}
				new Ajax.Request(this.ajaxUri, opt);
		    }

		}
	}