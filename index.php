<!doctype html>
<head>
  <meta charset="utf-8">

  <title>CarGet</title>
  <meta name="description" content="My Parse App">
  <link rel="stylesheet" type="text/css" href="global.css">
  <meta name="viewport" content="width=device-width">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="http://www.parsecdn.com/js/parse-1.2.12.min.js"></script>
</head>

<body>
  
  <script>

    Parse.initialize("bSshTjVhHBVfVwst7TkbzJx9c2tIL9fgP1ViH1EY", "Yx3XAdb5qVzhS7BpXadfBXD36SjlfxOokFjeFhub");

    function getLocation() {
     if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition( function(position){
          $.get("http://maps.googleapis.com/maps/api/geocode/json?latlng="+position.coords.latitude+","+position.coords.longitude+"&sensor=true",function(data,status){
            for (var i in data.results) {
              if (data.results[i].types[0]=="locality") {
                console.log(data);
                $('#city').val(data.results[i].address_components[0].long_name);
                $('#latlong').val(position.coords.latitude+","+position.coords.longitude);
                break;
              }
            }
          }); 
        });
      }
      else{
        console.log("Geolocation is not supported by this browser.");
      }
    }
    
      window.fbAsyncInit = function() {
        // init the FB JS SDK
        Parse.FacebookUtils.init({
          appId      : '333881846728290'                      // App ID from the app dashboard
        });

        // Additional initialization code such as adding Event Listeners goes here
      };

      // Load the SDK asynchronously
      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/all.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));

    $(document).ready(function(){

    $('#city').keydown( function(){
      console.log('misbehave');
      $('#latlong').val('');      
    });


    $('#wthrProceed').click(function(){
        $('#weatherstuff').hide();
        $('#journeyType').fadeIn();
        console.log('asd hap');
    });

      $('#login').click(function() {
        Parse.FacebookUtils.logIn("publish_actions", {
          success: function(user) {
              $('#weatherstuff').show();
              $('#login').hide();

               var usr = jQuery.parseJSON(user._hashedJSON.authData);
               $('#fbUser').val(usr.facebook.id);
               $('#fbAT').val(usr.facebook.access_token);
                          
          } //success
        }); // logIn 
      }); //login.click()

  

      $('#getLoc').click(function(){
        getLocation();
      });

      $('#like').click(function(){
        $.post("https://graph.facebook.com/"+$('#fbUser').val()+"/feed?link=http%3A%2F%2Fcg-kingshu2.rhcloud.com&picture="+$('#carImage').val()+"&message=I used CarGet to choose "+$('#carModel').val()+"&access_token="+$('#fbAT').val(),function(data,status){  
            console.log('posted');
            $('#approve').hide();
            $('#postedButton').fadeIn('slow');
        });
      });


      $('#submit').click(function(){
        if($('#city').val()!="") {
          if ($('#latlong').val()!='') {
            var qry="proxy.php?url=http%3A%2F%2Fapi.wunderground.com%2Fapi%2Fa74dcd0087397208%2Fconditions%2Fq%2F"+$('#latlong').val()+".json";
          }
          else {
            var qry="proxy.php?url=http%3A%2F%2Fapi.wunderground.com%2Fapi%2Fa74dcd0087397208%2Fconditions%2Fq%2F"+$('#city').val().split(', ')[1]+"%2F"+$('#city').val().split(', ')[0].replace(' ', '_')+".json";
          }
            console.log(qry);
            $.get(qry,function(data,status){
            data = jQuery.parseJSON(data);
            $('#localweather').append("<strong>"+data.current_observation.weather+"</strong>");
            $('#localweatherOut').fadeIn();
            $('#innerWeather').fadeOut();

            var rain = new Array("Rain","Drizzle","Rain Mist","Rain Showers","Snow Showers","Thunderstorms and Rain","Freezing Drizzle","Freezing Rain","Overcast","Unknown Precipitation");        
            var snow = new Array("Snow","Snow Grains","Ice Crystals","Ice Pellets","Hail","Blowing Snow","Snow Blowing Snow Mist","Ice Pellet Showers","Hail Showers","Small Hail Showers","Thunderstorm","Small Hail","Squalls","Funnel Cloud","Thunderstorms and Snow","Thunderstorms and Ice Pellets","Thunderstorms with Hail","Thunderstorms with Small Hail");
            var sunny = new Array("Clear","Partly Cloudy","Mostly Cloudy","Scattered Clouds");
            var dust = new Array("Mist","Fog","Fog Patches","Smoke","Volcanic Ash","Widespread Dust","Sand","Haze","Spray","Dust Whirls","Sandstorm","Low Drifting Snow","Low Drifting Widespread Dust","Low Drifting Sand","Blowing Widespread Dust","Blowing Sand","Freezing Fog","Patches of Fog","Shallow Fog","Partial Fog");
           
            window.recCarType = new Array();
            if (jQuery.inArray(data.current_observation.weather,rain)!='-1') {
                console.log("rain");
                window.recCarType.push("hatchback","sedan","coupe","pickup","van");
            }
            if (jQuery.inArray(data.current_observation.weather,sunny)!='-1') {
                console.log("sunny");
                window.recCarType.push("convertible","pickup","sedan","van");
            }
            if (jQuery.inArray(data.current_observation.weather,snow)!='-1') {
                console.log("snow");
                window.recCarType.push("pickup","van");
            }
            if (jQuery.inArray(data.current_observation.weather,dust)!='-1') {
                console.log("dust");
                window.recCarType.push("hatchback","van");
            }

          });
        }
        else {
          alert ("Please enter city");
        }
      });

      $('#recSubmit').click(function(){

          $.get("http://hearstcars.api.mashery.com/v1/api/vehicles/makes/all/json?api_key=vqktnnwjffejkdwqva9nuhrd",function(data,status){
             for (var i in data.vehicles.makes) {
              if (jQuery.inArray(data.vehicles.makes[i].name, $('#carDriven').val().split(" ") ) != -1 ) {
                $.get("http://hearstcars.api.mashery.com/v1/api/vehicles/models-by-make/"+data.vehicles.makes[i].name+"/json?api_key=vqktnnwjffejkdwqva9nuhrd",function(data,status){
                  for (var i in data.vehicles.models) {
                      if (jQuery.inArray(data.vehicles.models[i].name, $('#carDriven').val().split(" ") ) != -1 ) {
                        
                        
                        $.get("http://hearstcars.api.mashery.com/v1/api/vehicles/multivehicles-by-model/"+data.vehicles.models[i].id+"/json?api_key=vqktnnwjffejkdwqva9nuhrd", function(data){
                          
                          $('#carDetails').html("<img src="+data.submodels[0].image+" width=600 height=400> "+data.submodels[0].name );  

                        });

                      }
                  }
                });
                break;
              }
            }
          }); 
      });     

      $('.FinalLookup').click(function(){
        $('#journeyType').hide();
        window.recCarType2.filter(function(n) {
            return window.recCarType.indexOf(n) != -1
        });
        console.log(recCarType2);
        $.get("http://hearstcars.api.mashery.com/v1/api/perfdata/by-bodystyle/"+recCarType2[0]+"/json?api_key=vqktnnwjffejkdwqva9nuhrd", function(data){
           
          var recModel = data.models[Math.floor((Math.random()*data.models.length))].id;
          console.log("Model recommended");
          console.log(data);
          $('#recommendedModel').val(recModel);
          $.get("http://hearstcars.api.mashery.com/v1/api/vehicles/multivehicles-by-model/"+recModel+"/json?api_key=vqktnnwjffejkdwqva9nuhrd", function(data){
             console.log("Model Looked up");
             console.log(data);
             $('#carDetails').html("<img src="+data.submodels[0].image+" width=500 height=350><br><br><p class='lead'> "+data.submodels[0].name+"</p>");
             $('#finalresult').fadeIn();  
             $('#showperf').show();
             $('#carModel').val(data.submodels[0].name);
             $('#carImage').val(data.submodels[0].image);
          });
          $.get("http://hearstcars.api.mashery.com/v1/api/perfdata/by-id/"+$('#recommendedModel').val()+"/json?api_key=vqktnnwjffejkdwqva9nuhrd", function(data){
          /*  
            if (data.perfdata[0].top_speed=="" || !data.perfdata[0].top_speed ||  data.perfdata[0].top_speed == undefined) 
              var topspeed = "<Data not available>";
            else 
              var topspeed = data.perfdata[0].epaCity;
            if (data.perfdata[0].epaCity=="" || !data.perfdata[0].epaCity || data.perfdata[0].epaCity == undefined ) 
              var mileage = "<Data not available>";
            else 
              var mileage = data.perfdata[0].epaCity;
            if (data.perfdata[0].zero_to_sixty=="" || !data.perfdata[0].zero_to_sixty || data.perfdata[0].zero_to_sixty == undefined ) 
              var zero_to_sixty = "<Data not available>";
            else 
              var zero_to_sixty = data.perfdata[0].zero_to_sixty;
          */
            $("#perfDetails").html("<div class='panel panel-default center' style='width:300px'><div class='panel-body'>Top Speed: "+data.perfdata[0].top_speed+" mph</div><div class='panel-body'>0-60: "+data.perfdata[0].zero_to_sixty+" seconds</div><div class='panel-body'>City mileage: "+data.perfdata[0].epaCity+" miles/gallon</div></div>");
          });
        });
      }); 

      $('#showperf').click(function(){
        console.log("show stats");
        $('#showperf').hide();
        
        
      });
      
    });


    
    function jtype() {
       window.recCarType2 = new Array();
       if($('#jType').val()=='heavyDuty') {
          window.recCarType2.push("suv","pickup");
       }
       if($('#jType').val()=='family') {
          window.recCarType2.push("sedan","convertible","suv","van");
       }
       if($('#jType').val()=='fast') {
          window.recCarType2.push("coupe","convertible","sedan");
       }
       if($('#jType').val()=='economy') {
          window.recCarType2.push("hatchback","coupe");
       }

    }
  
  </script>
<style>
  .center{
    width:800px;
    margin:auto;
    text-align:center;
  }
  .fixedheight{
    height:300px;
  } 
  .fixedheight2{
    height:155px;
  }
</style>
<br><br>
<div class='center'> 
  <h1>CarGet</h1>
  <br><br>
  <button id='login' class="btn btn-primary btn-lg">Start</button>


<div id="fb-root"></div>
<div class="alert alert-dismissable alert-success fixedheight" id='weatherstuff' style='display:none'>
      
  <h3>Weather</h3>
  <div id='innerWeather'>
      Where are you driving today? <br><br>
      <center>
      <input class="form-control input-lg" style='width:50%' type='text' id='city' placeholder='City, ST'>
      </center>
      <input type='hidden' id='latlong'><br><br>
      <button class="btn btn-warning" id='getLoc'>Get Current City</button>
      &emsp;&emsp;
      <button class="btn btn-warning" id='submit'>Lookup Weather</button>
  </div>
      <br><br>
      
        <div id='localweatherOut' style='display:none'>
          <h4><div id='localweather'>The weather at your location is </div></h4>
          <br><br>
          <button id='wthrProceed' class="btn btn-warning">Proceed</button>
      </div>
</div>

<br>
<div style='display:none' id='journeyType' class="alert alert-dismissable alert-info fixedheight">
  <h3>Journey Type</h3><br>
  <center>
    <select id='jType' onchange='jtype()' class="form-control" style="width:200px">
      <option value=''></option>
      <option value='heavyDuty'>Heavy Duty</option>
      <option value='family'>Family / Luxury</option>
      <option value='fast'>Fast</option>
      <option value='economy'>Economy</option>
    </select>
  </center> 
<br><br>
      <!--div id='y-recommendation'>
              Direct search: <input type='text' id='carDriven'> <br>
              <input type="Submit" id='recSubmit' value="Search">
      </div-->

<button class="btn btn-default FinalLookup">Find Car</button>
</div>
<div style='display:none' id='finalresult'>
    <ul class="nav nav-tabs" style="margin-bottom: 50px;">
        <li class="disabled"><h4>We recommend-</h4></li>            
    </ul>
      <table border=0 cellpadding=20>
        <tr><td>
        
      <div id='carDetails'>
      </div>
        <br>
      </td><td valign=top>  
      <div id='perfDetails'>
      </div>
      <br>
      <div class='panel panel-default center fixedheight2' style='width:300px'>
        <div  id='approve'>
          <div class='panel-body'><button id='like' class="btn btn-success"><img src="http://www.clker.com/cliparts/2/7/d/5/1247117411176075605Symbol_thumbs_up.svg" height=15px width=15px> I like this</button></div>
          <div class='panel-body'><button class="btn btn-default FinalLookup">Show another</button></div>
        </div>
        <br><br><button type='button' id='postedButton' class='btn btn-info disabled' style='display:none'>Posted to Facebook</button>
      </div>
    </td></tr></table>
</div>

<input type='hidden' id='recommendedModel'>
<input type='hidden' id='fbUser'>
<input type='hidden' id='fbAT'>
<input type='hidden' id='carImage'>
<input type='hidden' id='carModel'>
</div>
</body>

</html>

