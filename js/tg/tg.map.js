
if (window.tg == undefined)
	var tg = {};

tg.maps = {};

tg.maps.Map = function (options)
{
	this.addresses = [];
	this.options = options;
	
	var defaultOptions = {
		map: {
			zoom: 8,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: new google.maps.LatLng(51.499396,-0.125806),
			maxZoom: 16,
		},
		container:'map',
		autoCenter:true,
		centerLast:false,
		clickable:true
    };
	
	this.options = $.extend(true, {}, defaultOptions, options);
	
	this.container = document.getElementById(options.container);
	
	if (this.container == undefined || this.container == null)
	{
		alert ('Container: "'+options.container+'" not found');
		return false;
	}
	
	this.map = new google.maps.Map(this.container,this.options.map);	

	this.bounds = new google.maps.LatLngBounds ();

	this.geocoder = new google.maps.Geocoder();

	var _this = this;
	
	this.addAddress = function (address)
	{
		if (address.location)
		{
			_this.addAddressCallback(address);
			return true;
		}
		if (address.mapCoord)
		{
			var mapCoord = address.mapCoord.split(",");
			address.location = new google.maps.LatLng(mapCoord[0],mapCoord[1]),
			_this.addAddressCallback(address);
			return true;
		}
		
		this.geocoder.geocode(
		{
			'address' : address.postcode
		},
		function(results, status) 
		{
			if (status == google.maps.GeocoderStatus.OK) {
				address.location = results[0].geometry.location;
				_this.addAddressCallback(address);
			} else {
				//alert("Geocode was not successful for the following reason: " + address.postcode);
			}
		});
	}
	
	this.addAddressCallback = function (address)
	{		
		var name = address["villageName"];
		
		var description = '<div class="open-day-popup" style="background-image:url('+address["villageThumbSrc"]+');">';
		description += '<h3>'+address["title"]+"</h3>";
		description +='<p>';
		
//		var date = fromIsoDate(address["date"]);
		
		description +=address["date"]+"<br />";
		description +=address["time"]+"<br />";
		description +=address["address"]+"<br />";
		description +='<a href="/villages/index/id/'+address["villageId"]+'" class="btnMoreInfo btn">More info</a><br />';
		description +='</p>';
		description +='</div>';
		
		var marker = new google.maps.Marker({
            map: this.map,
            position: address.location,
            draggable: false
        });

		if (this.options.autoCenter)
		{
			this.bounds.extend (address.location);
			this.map.fitBounds (this.bounds);
		} else if (this.options.centerLast)
		{
			this.map.setCenter (address.location);
		}
		
		var _this = this;
		if (this.options.clickable)
		{
			google.maps.event.addListener(marker, 'click', function() {
			      var infoBox = new InfoBox({latlng: marker.getPosition(), map: _this.map, content:description});
			});
		}
		
		address.marker = marker;
		this.addresses.push(address);
	}
	
	this.addAddresses = function (addresses)
	{  
		for (var key in addresses)
		{
			this.addAddress (addresses[key]);
		}
	}
	
	this.filterAddressesNear = function (center, range)
	{  	
		
		range = range*1000; // metres ?
		c(range)
		
		var bounds = new google.maps.LatLngBounds ();
		var center =  new google.maps.LatLng(-33.88717,151.27626);
		for (var key in addresses)
		{
			var address = this.addresses[key];
			var distance = google.maps.geometry.spherical.computeDistanceBetween(center, address.location);
			c (distance)
			
			if (distance < range)
			{
				c(address.title)
				bounds.extend (address.location);
			}
//			else
//				address.marker.hide ();
		}

		this.map.fitBounds (bounds);
	}
};





/* An InfoBox is like an info window, but it displays
 * under the marker, opens quicker, and has flexible styling.
 * @param {GLatLng} latlng Point to place bar at
 * @param {Map} map The map on which to display this InfoBox.
 * @param {Object} opts Passes configuration options - content,
 *   offsetVertical, offsetHorizontal, className, height, width
 */
function InfoBox(opts) {
  google.maps.OverlayView.call(this);
  this.latlng_ = opts.latlng;
  this.map_ = opts.map;
  this.offsetVertical_ = -270;
  this.offsetHorizontal_ = -400;
  this.height_ = 230;
  this.width_ = 417;
  this.content = opts.content;
 
  var me = this;
  this.boundsChangedListener_ =
    google.maps.event.addListener(this.map_, "bounds_changed", function() {
      return me.panMap.apply(me);
    });
 
  // Once the properties of this OverlayView are initialized, set its map so
  // that we can display it.  This will trigger calls to panes_changed and
  // draw.
  this.setMap(this.map_);
}
 
/* InfoBox extends GOverlay class from the Google Maps API
 */
InfoBox.prototype = new google.maps.OverlayView();
 
/* Creates the DIV representing this InfoBox
 */
InfoBox.prototype.remove = function() {
  if (this.div_) {
    this.div_.parentNode.removeChild(this.div_);
    this.div_ = null;
  }
};
 
/* Redraw the Bar based on the current projection and zoom level
 */
InfoBox.prototype.draw = function() {
  // Creates the element if it doesn't exist already.
  this.createElement();
  if (!this.div_) return;
 
  // Calculate the DIV coordinates of two opposite corners of our bounds to
  // get the size and position of our Bar
  var pixPosition = this.getProjection().fromLatLngToDivPixel(this.latlng_);
  if (!pixPosition) return;
 
  // Now position our DIV based on the DIV coordinates of our bounds
  this.div_.style.width = this.width_ + "px";
  this.div_.style.left = (pixPosition.x + this.offsetHorizontal_) + "px";
  this.div_.style.height = this.height_ + "px";
  this.div_.style.top = (pixPosition.y + this.offsetVertical_) + "px";
  this.div_.style.display = 'block';
};
 
/* Creates the DIV representing this InfoBox in the floatPane.  If the panes
 * object, retrieved by calling getPanes, is null, remove the element from the
 * DOM.  If the div exists, but its parent is not the floatPane, move the div
 * to the new pane.
 * Called from within draw.  Alternatively, this can be called specifically on
 * a panes_changed event.
 */
InfoBox.prototype.createElement = function() {
  var panes = this.getPanes();
  var div = this.div_;
  if (!div) {
    // This does not handle changing panes.  You can set the map to be null and
    // then reset the map to move the div.
    div = this.div_ = document.createElement("div");
    $(div).addClass("CustomInfoBox");
    div.style.border = "0px none";
    div.style.position = "absolute";
    div.style.width = this.width_ + "px";
    div.style.height = this.height_ + "px";
    var contentDiv = document.createElement("div");
    $(contentDiv).addClass("CustomInfoBoxContent");
    contentDiv.innerHTML = this.content;
 
    var closeImg = document.createElement("div");
    $(closeImg).addClass("CustomInfoBoxClose");
    closeImg.style.width = "32px";
    closeImg.style.height = "32px";
    closeImg.style.cursor = "pointer";
    closeImg.innerHTML="Close"
 
    function removeInfoBox(ib) {
      return function() {
        ib.setMap(null);
      };
    }
 
    google.maps.event.addDomListener(closeImg, 'click', removeInfoBox(this));
 
    div.appendChild(closeImg);
    div.appendChild(contentDiv);
    div.style.display = 'none';
    panes.floatPane.appendChild(div);
    this.panMap();
  } else if (div.parentNode != panes.floatPane) {
    // The panes have changed.  Move the div.
    div.parentNode.removeChild(div);
    panes.floatPane.appendChild(div);
  } else {
    // The panes have not changed, so no need to create or move the div.
  }
}
 
/* Pan the map to fit the InfoBox.
 */
InfoBox.prototype.panMap = function() {
  // if we go beyond map, pan map
  var map = this.map_;
  var bounds = map.getBounds();
  if (!bounds) return;
 
  // The position of the infowindow
  var position = this.latlng_;
 
  // The dimension of the infowindow
  var iwWidth = this.width_;
  var iwHeight = this.height_;
 
  // The offset position of the infowindow
  var iwOffsetX = this.offsetHorizontal_;
  var iwOffsetY = this.offsetVertical_;
 
  // Padding on the infowindow
  var padX = 40;
  var padY = 40;
 
  // The degrees per pixel
  var mapDiv = map.getDiv();
  var mapWidth = mapDiv.offsetWidth;
  var mapHeight = mapDiv.offsetHeight;
  var boundsSpan = bounds.toSpan();
  var longSpan = boundsSpan.lng();
  var latSpan = boundsSpan.lat();
  var degPixelX = longSpan / mapWidth;
  var degPixelY = latSpan / mapHeight;
 
  // The bounds of the map
  var mapWestLng = bounds.getSouthWest().lng();
  var mapEastLng = bounds.getNorthEast().lng();
  var mapNorthLat = bounds.getNorthEast().lat();
  var mapSouthLat = bounds.getSouthWest().lat();
 
  // The bounds of the infowindow
  var iwWestLng = position.lng() + (iwOffsetX - padX) * degPixelX;
  var iwEastLng = position.lng() + (iwOffsetX + iwWidth + padX) * degPixelX;
  var iwNorthLat = position.lat() - (iwOffsetY - padY) * degPixelY;
  var iwSouthLat = position.lat() - (iwOffsetY + iwHeight + padY) * degPixelY;
 
  // calculate center shift
  var shiftLng =
      (iwWestLng < mapWestLng ? mapWestLng - iwWestLng : 0) +
      (iwEastLng > mapEastLng ? mapEastLng - iwEastLng : 0);
  var shiftLat =
      (iwNorthLat > mapNorthLat ? mapNorthLat - iwNorthLat : 0) +
      (iwSouthLat < mapSouthLat ? mapSouthLat - iwSouthLat : 0);
 
  // The center of the map
  var center = map.getCenter();
 
  // The new map center
  var centerX = center.lng() - shiftLng;
  var centerY = center.lat() - shiftLat;
 
  // center the map to the new shifted center
  map.setCenter(new google.maps.LatLng(centerY, centerX));
 
  // Remove the listener after panning is complete.
  google.maps.event.removeListener(this.boundsChangedListener_);
  this.boundsChangedListener_ = null;
};
 

