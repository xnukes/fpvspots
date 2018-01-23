jQuery(function($) {

    $.nette.init();

	$.nette.ext('spinner', {
		init: function () {
			spinner = $('<div></div>', { id: "ajax-spinner" });
			spinner.appendTo("body");
		},
		before: function (settings, ui, e) {
			$("#ajax-spinner").css({
				display: "block",
				left: e.pageX,
				top: e.pageY
			});
		},
		complete: function () {
			$("#ajax-spinner").css({
				display: "none"
			});
		}
	});

	/** START FORM CONTROL - MapPlacePicker **/
	$(document).ready(function () {
        $('div.gmaps-container').each(function (i, e) {

            var mapContainer = $(e);
            var googleMapSelector = mapContainer.find('.google-map');
            var googleMapInput = mapContainer.find('input');
            var googleMapSelectorId = googleMapSelector.attr('id');

            var values = googleMapInput.val();

            if(values != '') {
                var vars = values.split(';');
                selLat = vars[0];
                selLng = vars[1];
                selZoom = Number(vars[2]);
            } else {
                selLat = 49.8235529;
                selLng = 15.5690703;
                selZoom = 8;
                GMaps.geolocate({
                    success: function(position) {
                        map.setCenter(position.coords.latitude, position.coords.longitude);
                        selLat = position.coords.latitude;
                        selLng = position.coords.longitude;
                    }
				})
			}

            var map = new GMaps({
                el: '#' + googleMapSelectorId,
                lat: selLat,
                lng: selLng,
				zoom: selZoom,
				cursor: 'pointer',
                mapType: 'hybrid',
                click: function(e) {
                    googleMapInput.val(e.latLng.lat() + ';' + e.latLng.lng() + ';' + map.getZoom());
                    if(map.markers[0] !== undefined) {
                        map.markers[0].setPosition(new google.maps.LatLng(e.latLng.lat(), e.latLng.lng()));
                    } else {
                        map.addMarker({
                            lat: e.latLng.lat(),
                            lng: e.latLng.lng(),
                            title: 'Označené místo'
                        });
					}
                }
            });

            map.addMarker({
                lat: selLat,
                lng: selLng,
                title: 'Označené místo'
            });
        });
    });
    /** END FORM CONTROL - MapPlacePicker **/

});