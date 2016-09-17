var map = null;
var poly = null;
var bounds = null;
var boundsNotSet = true;
var multiply = 1;
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

        if (typeof points === 'undefined') {
            var points = 23454;
        }
        
        if (typeof tripPoints === 'undefined') {
            var tripPoints = 0;
        }
        
        $.getJSON('http://172.31.2.19:8000/app.php/trip', function(data) {
            if (data.length) {
                var path = poly.getPath();
                var position = null;
                var pointsPart = 0;
                var time = new Date();
                for (var i = 0; i < data.length; i++) {
                    position = new google.maps.LatLng(data[i].lat,data[i].lng);
                    path.push(position);
                    bounds.extend(position);

                    if (data[i].violations.length || data[i].obedience.length) {
                        if (map) {
                            placeOnMap(data[i]);
                        } else {
                            markers.push(data[i]);
                        }
                    }

                    pointsPart = data[i].points * multiply;
                    time.setTime(data[i].timestamp * 1000);
                }

                pointsPart = Math.round(pointsPart);
                points += pointsPart;
                tripPoints += pointsPart;

                if (pointsPart < 0) {
                    $welcomeScreen.toggleClass("redBackground");
                    setTimeout(function(){
                        $welcomeScreen.toggleClass("redBackground");
                     },100);
                } else if (pointsPart >= 10) {
                    $welcomeScreen.toggleClass("greenBackground");
                    setTimeout(function(){
                        $welcomeScreen.toggleClass("greenBackground");
                     },100);
                }

                poly.setPath(path);

                if (boundsNotSet && map) {
                    boundsNotSet = false;
                    map.fitBounds(bounds);
                }

                $welcomeScreen.find('.pointsTotal').html(points);
                $welcomeScreen.find('.place').html('44262');

                $('#map').find('.status').find('.time').html(dateToDMY(time));

                $lvls.find('.level').removeClass('active');

                if (tripPoints > 0) {
                    $lvls.find('.level1').addClass('active');
                    multiply = 1;
                }

                if (tripPoints > 100) {
                    $lvls.find('.level2').addClass('active');
                    multiply = 1.1;
                }

                if (tripPoints > 500) {
                    $lvls.find('.level3').addClass('active');
                    multiply = 1.2;
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

                $welcomeScreen.find('.multiplier').html(multiply);
            }
            
            setTimeout(function() { 
                app.updateState(points, tripPoints); 
            }, 15000);
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

$( document ).on('click', '#map a', function() {
    map.fitBounds(bounds);
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
    
    content = '';
    
    for (var i = 0; i < data.violations.length; i++) {
        content += data.violations[i].desc + '<br/>';
    }
    
    for (var i = 0; i < data.obedience.length; i++) {
        content += data.obedience[i].desc + '<br/>';
    }
    
    var infowindow = new google.maps.InfoWindow({
        content: content
    });

    var marker = new google.maps.Marker({
        position: position,
        map: map,
        title: 'Road event'
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}

function dateToDMY(date) {
    var d = date.getDate();
    var m = date.getMonth() + 1;
    var y = date.getFullYear();
    var hour = date.getHours();
    var minute = date.getMinutes();
    return (hour <= 9 ? '0' + hour : hour) + ':' + 
            (minute <= 9 ? '0' + minute : minute) + ' ' + 
            (d <= 9 ? '0' + d : d) + '-' + (m<=9 ? '0' + m : m) + '-' + '' + y;
}