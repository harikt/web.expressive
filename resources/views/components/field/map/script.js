Dms.form.initializeCallbacks.push(function (element) {

    var disableZoomScrollingUntilHoveredFor = function (milliseconds, googleMap) {
        googleMap.set('scrollwheel', false);
        var timeout;
        $(googleMap.getDiv()).hover(function () {
                timeout = setTimeout(function () {
                    googleMap.set('scrollwheel', true);
                }, milliseconds);
            },
            function () {
                clearTimeout(timeout);
                googleMap.set('scrollwheel', false);
            });
    };

    element.find('.dms-map-input').each(function () {
        var mapInput = $(this);

        var inputMode = mapInput.attr('data-input-mode');
        var latitudeInput = mapInput.find('input.dms-lat-input');
        var longitudeInput = mapInput.find('input.dms-lng-input');
        var currentLocationButton = mapInput.find('.dms-current-location');
        var fullAddressInput = mapInput.find('input.dms-full-address-input');
        var addressSearchInput = mapInput.find('input.dms-address-search');
        var mapCanvas = mapInput.find('.dms-map-picker');
        var forceSetAddress = false;

        var addressPicker = new AddressPicker({
            regionBias: 'AUS',
            map: {
                id: mapCanvas.get(0),
                zoom: 12,
                center: new google.maps.LatLng(
                    latitudeInput.val() || mapInput.attr('data-default-latitude') || -26.4390917,
                    longitudeInput.val() || mapInput.attr('data-default-longitude') || 133.281323), // Default to australia
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                draggable: !(mapCanvas.attr('data-no-touch-drag') && Dms.utilities.isTouchDevice())
            },
            marker: {
                draggable: true,
                visible: true
            },
            reverseGeocoding: true,
            autocompleteService: {
                autocompleteService: {
                    types: ['(cities)', '(regions)', 'geocode', 'establishment']
                }
            }
        });
        mapCanvas.data('map-api', addressPicker.getGMap());

        addressSearchInput.typeahead(null, {
            displayKey: 'description',
            source: addressPicker.ttAdapter()
        });

        addressSearchInput.bind("typeahead:selected", addressPicker.updateMap);
        addressSearchInput.bind("typeahead:cursorchanged", addressPicker.updateMap);
        addressPicker.bindDefaultTypeaheadEvent(addressSearchInput);

        $(addressPicker).on('addresspicker:selected', function (event, result) {
            if (!forceSetAddress && addressSearchInput.val() === '') {
                addressSearchInput.typeahead('val', '');
                latitudeInput.val('');
                longitudeInput.val('');
                fullAddressInput.val('');
                return;
            }

            forceSetAddress = false;

            if (addressSearchInput.is('[data-map-zoom]')) {
                addressPicker.getGMap().setCenter(new google.maps.LatLng(result.lat(), result.lng()));
                addressPicker.getGMap().setZoom(parseInt(addressSearchInput.attr('data-map-zoom'), 10));
            }
            latitudeInput.val(result.lat());
            longitudeInput.val(result.lng());
            var address = result.address();

            if (result.placeResult.name && address.indexOf(result.placeResult.name) === -1) {
                address = result.placeResult.name + ', ' + address;
            }

            addressSearchInput.val(address);
            fullAddressInput.val(address);
        });

        google.maps.event.addListener(addressPicker.getGMarker(), "dragend", function (event) {
            forceSetAddress = true;
        });

        var triggerReverseGeocode = function () {
            forceSetAddress = true;
            addressPicker.markerDragged();
            addressPicker.getGMap().setZoom(12);
        };

        if (navigator.geolocation) {
            currentLocationButton.click(function () {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var location = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    addressPicker.getGMarker().setPosition(location);
                    addressPicker.getGMap().setCenter(location);
                    triggerReverseGeocode();
                });
            });
        } else {
            currentLocationButton.prop('disabled', true);
        }

        if (latitudeInput.val() || longitudeInput.val()) {
            if (inputMode === 'lat-lng') {
                forceSetAddress = true;
                addressPicker.markerDragged();
            }

            if (inputMode === 'address-with-lat-lng') {
                var location = new google.maps.LatLng(latitudeInput.val(), longitudeInput.val());
                addressPicker.getGMarker().setPosition(location);
                addressPicker.getGMap().setCenter(location);
                addressSearchInput.val(fullAddressInput.val());
            }
        }

        addressSearchInput.change(function () {
            addressPicker.markerDragged();
        });

        disableZoomScrollingUntilHoveredFor(1000, addressPicker.getGMap());

        google.maps.event.addListenerOnce(addressPicker.getGMap(), 'idle', function(){
            if (fullAddressInput.val()) {
                addressSearchInput.typeahead('val', fullAddressInput.val());
            }
        });

        if (inputMode === 'address' && fullAddressInput.val()) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': fullAddressInput.val()}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    addressPicker.getGMap().setCenter(results[0].geometry.location);
                    addressPicker.getGMarker().setPosition(results[0].geometry.location);
                }
            });
        }
    });

    $('.dms-display-map').each(function () {
        var mapCanvas = $(this);

        var location = new google.maps.LatLng(mapCanvas.attr('data-latitude'), mapCanvas.attr('data-longitude'));
        var map = new google.maps.Map(mapCanvas.get(0), {
            center: location,
            zoom: parseInt(mapCanvas.attr('data-zoom'), 10) || 14,
            scrollwheel: false
        });

        disableZoomScrollingUntilHoveredFor(1000, map);

        mapCanvas.data('map-api', map);

        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: mapCanvas.attr('data-title')
        });
    });
});