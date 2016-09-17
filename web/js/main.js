
function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 3,
        center: {lat: 0, lng: -180},
        mapTypeId: 'terrain'
    });

    var section = {
        lat: 37.772,
        lng: -122.214,
        type: 1,
        marker: 'Too big acceleration'
    };

    var tracePoints = [
        [{lat: 37.772, lng: -122.214}, {lat: 21.291, lng: -157.821}],
        [{lat: 21.291, lng: -157.821}, {lat: -18.142, lng: 178.431}],
        [{lat: -18.142, lng: 178.431}, {lat: -27.467, lng: 153.027}]
    ];

    // var flightPlanCoordinates = [
    //     {lat: 37.772, lng: -122.214},
    //     {lat: 21.291, lng: -157.821},
    //     {lat: -18.142, lng: 178.431},
    //     {lat: -27.467, lng: 153.027}
    // ];

    $.each(tracePoints, function(index, item) {
        console.log(item);
        var flightPath = new google.maps.Polyline({
            path: item,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        flightPath.setMap(map);
    });

// Notice that index 2 is skipped since there is no item at
// that position in the array.
//     tracePoints.forEach(addTracePointsToMap);


}

$(document).ready(function(){


    initMap();
});

