
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
        var $welcomeScreen = $('.welcomeScrren');
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