var map = null,
    latitude = null,
    longitude = null,
    infoWindow = null;

function initialize() {

    var latlng = null;
    var myOptions = {
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("map"), myOptions);

    latlng = new google.maps.LatLng(latitude, longitude);
    map.setCenter(latlng);
}

function placeSpotsOnMap() {

    // Place Spots on the Map
    var url = ppi.baseUrl + 'foursquare/getVenues/lat/' + latitude + '/lng/' + longitude;
    $.getJSON(url, function (json) {
        addMarker(json);
    });

    latlng = new google.maps.LatLng(latitude, longitude);
    map.setCenter(latlng);
    map.setZoom(10);
}

function addMarker(json) {

    var venues = json.venues;

    for (i = 0; i < venues.length; i++) {
  
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(
                venues[i].location.lat,
                venues[i].location.lng
            ),
            map:      map,
            title:    venues[i].name,
            animation:google.maps.Animation.DROP
        });        

        (function (i, marker) {
 
            google.maps.event.addListener(marker, 'click', function () {
 
                if (!infoWindow) {
                    infoWindow = new google.maps.InfoWindow();
                }
 
                var desc = "<div class='spotsInfo'>" +
                    "<div class='title'><h2>" + venues[i].name + 
                    "<small> " + venues[i].categories[0].name + "</small>" +
                    "</h2></div>" + "<p>" + venues[i].location.address + 
                    ", " + venues[i].location.city + "</p>" +
                    "</div>";
 
                infoWindow.setContent(desc);
                infoWindow.open(map, marker);
            });
        })(i, marker);
    }
}

function useDefaultLocation() {
    latitude = 31.8391;
    longitude = -106.5631;
}

jQuery(document).ready(function ($) {
    var geo = false;
    //check if the geolocation object is supported, if so get position
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            geo = true;
            latitude = position.coords.latitude;
            longitude = position.coords.longitude;  
            initialize();
            placeSpotsOnMap();
        }, function (e) {
            useDefaultLocation();
        });
    } else {
        // browser don't support geo location.
        useDefaultLocation();
    }
    if(!geo) {
        initialize();
        placeSpotsOnMap();
    }
});
