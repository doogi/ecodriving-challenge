
var app = {
    initialize: function() {
        this.bindEvents();
    },
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    onDeviceReady: function() {
        app.updateState();
    },
    updateState: function() {
        var $welcomeScreen = $('#homePage');
        var $lvls = $welcomeScreen.find('.lECOvel');
        $.getJSON('http://172.31.2.19:8000/test.json', function(data) {
           if (data.currentLevel) {
               $welcomeScreen.find('.currentLevel').html(data.currentLevel);

               $lvls.find('.level').removeClass('active');
               for (var i = 1; i <= data.currentLevel; i++) {
                   $lvls.find('.level' + i).addClass('active');
               }

               $welcomeScreen.find('.pointsEarning').html(data.pointsPerMinute);
               $welcomeScreen.find('.multiplayer').html(data.multiplayer);
               $welcomeScreen.find('.pointsTotal').html(data.points);
               $welcomeScreen.find('.place').html(data.place);
               $welcomeScreen.find('.nextLevel').html(data.nextLevel);

               if (data.state === "Driving") {
                   $welcomeScreen.find('.seeYourTrip').show();
               } else {
                   $welcomeScreen.find('.seeYourTrip').hide();
               }
           } 

           setTimeout(function() { app.updateState(); }, 10000);
        });
    }
};

$(document).on("pageshow","#map",function(){ 
    if (!$(this).hasClass('initialized')) {
        $(this).addClass('initialized');
        var map = initialize();

        $.getJSON('http://172.31.2.19:8000/app.php/trip', function(data) {
            poly = new google.maps.Polyline({
                strokeColor: '#000000',
                strokeOpacity: 1.0,
                strokeWeight: 3
            });

            var path = poly.getPath();
            var bounds = new google.maps.LatLngBounds();
            var position = null;

            for (var i = 0; i < data.length; i++) {
                position = new google.maps.LatLng(data[i].lat,data[i].lng);
                path.push(position);
                bounds.extend(position);

                if (typeof data[i].violations !== 'undefined' && data[i].violations.length) {
                    var infowindow = new google.maps.InfoWindow({
                      content: data[i].violations[0].desc
                    });

                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: data[i].violations[0].desc
                    });

                    marker.addListener('click', function() {
                      infowindow.open(map, marker);
                    });
                    }
            }

            poly.setMap(map);
            map.fitBounds(bounds);
        });
    }
});

function initialize()
{
    var map = showMap();
    return map;
}

function showMap()
{
    var mapOptions = {
        zoom: 14,
        center: new google.maps.LatLng(47.389822, 8.515603),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

    return map;
}