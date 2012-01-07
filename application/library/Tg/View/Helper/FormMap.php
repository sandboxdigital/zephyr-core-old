<?php

/**
 * Helper to generate a "file" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Tg_View_Helper_FormMap extends Zend_View_Helper_FormFile
{
    public function formMap($name, $value = null, $attribs = null)
    {
    	$homePoint = '51.51131,-0.13046'; // London
    	$homePoint = '-33.87554,151.20896'; // Sydney
        $info = $this->_getInfo($name, $value, $attribs);

        extract($info); // name, id, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }
        
        if (empty($value))
        	$value = $homePoint;
        
       	$lat = $long = '';
       	$isCoords = false;
        $coords = explode (",",$value);
        if (count($coords)==2)
        {
        	$lat = $coords[0];
        	$long = $coords[0];
        	$isCoords=is_numeric($lat)&&is_numeric($long);
        }
        
        // build the element
//        $key = 'ABQIAAAAWX9afFCa6bUGcvQn2jOd5RS0JTIC8IBd-3X9m9yaWhTcex8gtBQFAWM-yzQ7yOKjZlTFkH9p5asj_Q';
        
		$this->view->headScript()->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false');
        
        $xhtml = '<div id="'.$this->_normalizeId($name).'_map" style="float:left;width:100%;height:200px;">xxx</div>';
        $xhtml .= '<div style="width:100%;">';
        $xhtml .= $this->_hidden($name, $value, array ('id'=>$this->_normalizeId($name).'_hidden'));
        $xhtml .= 'Long,Lat: <input id="'.$this->_normalizeId($name).'_lat" class="textTiny" value="'.$lat.'" style="width:70px" /> , <input id="'.$this->_normalizeId($name).'_lng" class="textTiny" style="width:70px" value="'.$long.'" /><br />';
        $xhtml .= 'Search: <input id="'.$this->_normalizeId($name).'_searchbox" class="textSmall" /><input id="'.$this->_normalizeId($name).'_searchbutton" type="button" value="Search" />';
        $xhtml .= '</div><div style="clear:both;"></div>';
		$xhtml .= '<script>
var homePoint = null
var map = null;
var marker = null;
var defaultZoom = 7;

function googleMapInit() {
	var options = {};
	
	homePoint = new google.maps.LatLng('.$homePoint.');
	
	var defaultOptions = {
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: homePoint,
		maxZoom: 16,
	};
	
	options = $.extend(true, {}, defaultOptions, options);

	map = new google.maps.Map(document.getElementById("'.$this->_normalizeId($name).'_map"), options);
';	
	if ($isCoords)
$xhtml .= '		addPeg (new google.maps.LatLng('.$value.'), 15);';
	else
$xhtml .= '		addPeg (homePoint, defaultZoom);addressSearch ("'.$value.'");';
$xhtml .= '
		//
	//
}

function addPeg (latLng, zoom)
{
	marker = new google.maps.Marker(latLng, {draggable: true});
	
	marker = new google.maps.Marker({
            map: map,
            position: latLng,
            draggable: true
        });
	
	google.maps.event.addListener(marker, "dragend", function() {
		storeLatLng(marker.getPosition())
	});
  	
	//storeLatLng (latLng);
	
	map.setCenter(latLng, zoom);
}

function storeLatLng (latLng)
{
	var lat = latLng.lat().toFixed(5);
	var lng = latLng.lng().toFixed(5);
	var both = lat+","+lng;
	
	$("#'.$this->_normalizeId($name).'_hidden").val(both);
	$("#'.$this->_normalizeId($name).'_lat").val(lat);
	$("#'.$this->_normalizeId($name).'_lng").val(lng);
}

function addressSearch(address) {
 	var geocoder = new google.maps.Geocoder(); 	
	geocoder.geocode(
	{
		"address" : address
	},
	function(results, status) 
	{
		if (status == google.maps.GeocoderStatus.OK) {
			point = results[0].geometry.location;
	        marker.setPosition (point);
			storeLatLng(point);
			map.setCenter(point);
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});
}

$("#'.$this->_normalizeId($name).'_searchbutton").click(function(){
	addressSearch ($("#'.$this->_normalizeId($name).'_searchbox").val());
});

googleMapInit();

</script>';

        return $xhtml;
    }
}
