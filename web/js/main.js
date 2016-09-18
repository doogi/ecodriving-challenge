

// $.getJSON('http://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Washington,DC&destinations=New+York+City,NY&key=AIzaSyChrZ5bHmlxc8rbcBplhIS95NS1r5MFN_o', function(googleData) {
//     console.log(googleData);
// });

var map;

function clone(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
    }
    return copy;
}

var mapGenerator = {
    points: {
        added: 0,
        removed: 0,
        total: 0
    },
    summary: {
        distance: 0,
        totalTime: 0
    },
    flags: {
        jsonEnd: false
    },
    track: {
        date: '',
        from: '',
        to: ''
    },
    poly: null,
    bounds: null,
    markers: [],
    counter: 0,
    originsCount: 0,
    startLat: 0,
    startLng: 0,
    initialize: function() {
        this.getPoints();
    },
    getTotalTime: function(data) {
        var firstResult = data[Object.keys(data)[0]];
        var lastResult = data[Object.keys(data)[data.length-1]];

        var timeStart = moment.unix(firstResult.timestamp);
        var timeStop = moment.unix(lastResult.timestamp);

        this.track.date = moment.unix(firstResult.timestamp).format("YYYY-MM-DD");

        var diffHours = Math.abs(timeStart.diff(timeStop, 'hours'));
        var diffMinutes = Math.abs(timeStart.diff(timeStop, 'minutes')) - diffHours*60;
        var diffSeconds = Math.abs(timeStart.diff(timeStop, 'seconds')) - diffHours*60*60 - diffMinutes*60;

        var totalTime = '';
        if(diffHours > 0) {
            totalTime += diffHours+'h '
        }
        if(diffMinutes > 0) {
            totalTime += diffMinutes+'min '
        }
        if(diffSeconds > 0) {
            totalTime += diffSeconds+'min '
        }
        return totalTime;
    },
    getAddressByCoordinates: function(data, type) {
        this.startLat = data.lat;
        this.startLng = data.lng;
        var latlng = {lat: data.lat, lng: data.lng};
        var geocoder= new google.maps.Geocoder();
        var that = this;
        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    if(type === 'from') {
                        that.track.from = results[0].formatted_address;
                    }
                    if(type === 'to') {
                        that.track.to = results[0].formatted_address;
                    }
                }
            }
        });
    },
    getPoints: function(){

        var that = this;

        // do {
            $.getJSON('app.php/trips', function (data) {
                if (data.length) {

                    that.totalTime = that.getTotalTime(data);
                    that.getAddressByCoordinates(data[Object.keys(data)[0]], 'from');
                    that.getAddressByCoordinates(data[Object.keys(data)[data.length-1]], 'to');

                    $.each(data, function (index, item) {
                        if(item.points > 0) {
                            that.points.added += item.points;
                        }
                        if(item.points < 0) {
                            that.points.removed += item.points;
                        }
                        that.points.total += item.points;
                    });






                    var origins = [];
                    var origins2 = [];
                    var origins3 = [];
                    $.each(data, function (index, item) {
                        if(index%500 === 0 && $.inArray('lat'+item.lng+item.lat, origins2) === -1){
                            origins2.push('lat'+item.lng+item.lat);
                            origins3.push([item.lng, item.lat]);
                            origins.push(new google.maps.LatLng(item.lat, item.lng));
                        }
                    });

                    destinations = clone(origins);
                    origins.pop();
                    destinations.splice(0, 1);



                    that.originsCount = origins.length;

                    $.each(origins, function(ind, ite){
                        var service = new google.maps.DistanceMatrixService();
                        service.getDistanceMatrix(
                            {
                                origins: [ite],
                                destinations: [destinations[ind]],
                                travelMode: 'DRIVING'

                            }, callback);
                    });


                    function callback(response, status) {
                        that.counter++;
                        that.summary.distance += response.rows[0].elements[0].distance.value;

                        if(that.counter === that.originsCount) {
                            checkJsonEnd();

                            that.poly = new google.maps.Polyline({
                                strokeColor: '#000000',
                                strokeOpacity: 1.0,
                                strokeWeight: 3
                            });

                            that.bounds = new google.maps.LatLngBounds();

                            var path = that.poly.getPath();


                            position = new google.maps.LatLng(data[Object.keys(data)[0]].lat,data[Object.keys(data)[0]].lng);
                            path.push(position);
                            that.bounds.extend(position);

                            $.each(data, function (index, item) {

                                position = new google.maps.LatLng(item.lat,item.lng);
                                path.push(position);

                                if (item.violations.length || item.obedience.length) {
                                    if (map) {
                                        placeOnMap(item);
                                    } else {
                                        that.markers.push(item);
                                    }
                                }
                            });

                            that.poly.setPath(path);

                            that.poly.setMap(map);
                            // map.fitBounds(that.bounds);
                        }
                    }










                }


            });



        // } while(emptyResult < 100);
    }

};
var icons = {
    neutral: {
        icon: 'img/pin.png'
    },
    plus: {
        icon: 'img/pin_ok.png'
    },
    minus: {
        icon: 'img/pin_not_ok.png'
    }
};

function placeOnMap(data) {
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

    var currentIcon = icons.neutral.icon;
    if(data.points > 0) {
        currentIcon = icons.plus.icon;
    }
    if(data.points < 0) {
        currentIcon = icons.minus.icon;
    }

    var marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: currentIcon,
        title: 'Road event'
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}

function checkJsonEnd(){

    var result = {
        track: {
            date: mapGenerator.track.date,
            from: mapGenerator.track.from,
            to: mapGenerator.track.to
        },
        summary: {
            time: mapGenerator.totalTime,
            distance: mapGenerator.summary.distance/1000+' km'
        },
        points: {
            added: mapGenerator.points.added,
            removed: mapGenerator.points.removed,
            total: mapGenerator.points.total
        }
    };

    var artistTemplate = _.template($('#single-result').html());
    $('#accordion .panel-default').append(artistTemplate( result ));
    initMap();
}


function initMap() {
    var m = document.getElementById('map');
    map = new google.maps.Map(m, {
        zoom: 9,
        center: {lat: mapGenerator.startLat, lng: mapGenerator.startLng},
        mapTypeId: 'terrain'
    });
}

$(document).ready(function(){
    mapGenerator.initialize();
});

