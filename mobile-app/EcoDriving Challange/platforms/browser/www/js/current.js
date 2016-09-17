
var app = {
    initialize: function() {
        this.bindEvents();
    },
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    onDeviceReady: function() {
        var map = new app.GoogleMap();
        map = map.initialize();
        
        $.getJSON('http://172.31.2.19:8000/index.php/trip', function(data) {
            poly = new google.maps.Polyline({
                strokeColor: '#000000',
                strokeOpacity: 1.0,
                strokeWeight: 3
            });
             
            var path = poly.getPath();
            var bounds = new google.maps.LatLngBounds();

            for (var i = 0; i < data.length; i++) {
                path.push(new google.maps.LatLng(data[i].lat,data[i].lng));
                bounds.extend(new google.maps.LatLng(data[i].lat,data[i].lng));
            }

            poly.setMap(map);
            map.fitBounds(bounds);
        });
    },
    GoogleMap: function() {
        this.initialize = function(){
            var map = showMap();
            return map;
        }

        var showMap = function(){
            var mapOptions = {
                zoom: 14,
                center: new google.maps.LatLng(47.389822, 8.515603),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

            return map;
        }
    }
};