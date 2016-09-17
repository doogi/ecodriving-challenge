var map = null;
var poly = null;
var bounds = null;
var boundsNotSet = true;
var markers = [];

var app = {
    initialize: function() {
        this.bindEvents();
    },
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    onDeviceReady: function() {
        initialize();
        app.updateState();
    },
    updateState: function(points, tripPoints) {
        var $welcomeScreen = $('#homePage');
        var $lvls = $welcomeScreen.find('.lECOvel');
//        $.getJSON('http://172.31.2.19:8000/test.json', function(data) {
//           if (data.currentLevel) {
//               $welcomeScreen.find('.currentLevel').html(data.currentLevel);
//
//               $lvls.find('.level').removeClass('active');
//               for (var i = 1; i <= data.currentLevel; i++) {
//                   $lvls.find('.level' + i).addClass('active');
//               }
//
//               $welcomeScreen.find('.multiplier').html(data.multiplayer);
//               $welcomeScreen.find('.pointsTotal').html(data.points);
//               $welcomeScreen.find('.place').html(data.place);
//               $welcomeScreen.find('.nextLevel').html(data.nextLevel);
//
//               if (data.state === "Driving") {
//                   $welcomeScreen.find('.seeYourTrip').show();
//               } else {
//                   $welcomeScreen.find('.seeYourTrip').hide();
//               }
//           } 
//
//           setTimeout(function() { app.updateState(); }, 10000);
//        });

        if (typeof points === 'undefined') {
            var points = 23454;
        }
        
        if (typeof tripPoints === 'undefined') {
            var tripPoints = 0;
        }
        
        $.getJSON('http://172.31.2.19:8000/index.php/trip', function(data) {
            
            var path = poly.getPath();
            var position = null;

            for (var i = 0; i < data.length; i++) {
                position = new google.maps.LatLng(data[i].lat,data[i].lng);
                path.push(position);
                bounds.extend(position);

                if (typeof data[i].violations !== 'undefined' && data[i].violations.length) {
                    if (map) {
                        placeOnMap(data[i]);
                    } else {
                        markers.push(data[i]);
                    }
                }
                
                points += data[i].points;
                tripPoints += data[i].points;
            }
            
            poly.setPath(path);
            
            if (boundsNotSet && map) {
                boundsNotSet = false;
                map.fitBounds(bounds);
            }
            
            $welcomeScreen.find('.pointsTotal').html(points);
            $welcomeScreen.find('.place').html('44262');
            
            $lvls.find('.level').removeClass('active');
            
            if (tripPoints > 40) {
                $lvls.find('.level1').addClass('active');
            }
            
            if (tripPoints > 100) {
                $lvls.find('.level2').addClass('active');
            }
            
            if (tripPoints > 500) {
                $lvls.find('.level3').addClass('active');
            }
            
            if (tripPoints <= 0) {
                $lvls.find('.leveln1').addClass('active');
            }
            
            if (tripPoints <= -30) {
                $lvls.find('.leveln2').addClass('active');
            }
            
            if (tripPoints <= -100) {
                $lvls.find('.leveln3').addClass('active');
            }
            
            setTimeout(function() { app.updateState(points, tripPoints); }, 1000);
        });
    }
};

$( document ).on( "swipeleft", '#homePage', function() {
    $.mobile.changePage( "#map", { transition: "slide" });
});

$( document ).on( "swiperight", '#homePage', function() {
    $.mobile.changePage( "#map", { transition: "slide", reverse: true } );
});

$( document ).on( "swipeleft", '#map', function() {
    $.mobile.changePage( "#homePage", { transition: "slide" });
});

$( document ).on( "swiperight", '#map', function() {
    $.mobile.changePage( "#homePage", { transition: "slide", reverse: true } );
});
        
$(document).on("pageshow","#map",function(){ 
    if (!$(this).hasClass('initialized')) {
        $(this).addClass('initialized');
        map = showMap();
    }
});

function initialize()
{
    poly = new google.maps.Polyline({
        strokeColor: '#000000',
        strokeOpacity: 1.0,
        strokeWeight: 3
    });
            
    bounds = new google.maps.LatLngBounds();
}

function showMap()
{
    if (poly === null) {
        initialize();
    }
    
    var mapOptions = {
        zoom: 14,
        center: new google.maps.LatLng(47.389822, 8.515603),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    
    poly.setMap(map);
    map.fitBounds(bounds);
            
    for (var i = 0; i < markers.length; i++) {
        placeOnMap(markers[i], map);
    }
    
    return map;
}

function placeOnMap(data, map) {
    position = new google.maps.LatLng(data.lat,data.lng);
    
    var infowindow = new google.maps.InfoWindow({
        content: data.violations[0].desc
    });

    var marker = new google.maps.Marker({
        position: position,
        map: map,
        title: data.violations[0].desc
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}