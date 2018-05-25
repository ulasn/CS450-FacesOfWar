<?php

$conn = new mysqli("localhost","root","","fow");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$query ="Select startdate, enddate, deaths, lat, lon, nationality, location, civilianrate, tags, airforce, airrate, notes from deaths order by deaths desc";
$result = $conn->query($query);

$deaths = array();

$id = 0;
while($row = $result->fetch_row())
{
    $id = $id +1;
    
    $deaths[] = array("id" => $id, "startdate" => $row[0], "enddate" => $row[1], "deaths" => $row[2], "lat" => $row[3], "lon" => $row[4], "nationality" =>$row[5], "location" => $row[6], "civilianrate" => $row[7], "tags" => $row[8], "airforce" => $row[9], "airrate" => $row[10], "notes" => $row[11]);
    
}

$js_array = json_encode($deaths);
    
?>


<!DOCTYPE html>
<html>

<head>
<title>Faces Of War  </title>


    <script src="lib/d3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="js/slider.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/sliders.css">
    <style>body{overflow:hidden;}</style>
</head>

<body>
   
    
<div id="sidebar">
  <br>
  <div class="facesofwar">
      <a href="index.php"><img src="img/sidebar/fow.png" width="270px" height="200px"></a>
   </div>
   
    <ul>
       <hr width="50%" color="#FAFFD4">

        <li>
            <a href="index.php">CASUALTY</a>
        </li>
        <br>
        <li>
            <a href="propaganda.php">PROPAGANDA</a>
        </li>    
        <br>
        <li>
            <a href="posters.php">POSTERS</a>
        </li>
        <br>
        <li>
            <a href="#">ABOUT</a>
        </li>
        <hr width="50%" color="#FAFFD4">

    </ul>
   


    <center style="margin-top:380px;"><small class="text-muted">Casualty data taken by fallen.io</small></center>
</div>          
        
            
<div id="content">

<div class="card text-white bg-dark mb-3">
  <div class="card">
   <br>
    <center><h3>WORLD WAR II CASUALTIES</h3></center>
    <center><p class="card-text">Infographic about World War II Casualties with dates and locations
    <br>Unknown locations of casualties are shown on the center of their countries, some of the death toll data is collected after the war by population census they are shown at 1945</p></center><br>
    
   </div>
  <div class="card-body">
  
  <div id="map-holder">

<div id="resetbutton" class="resetbutton"><button class="btn" id="reset">Reset</button></div>
   <div id="buttons" class="buttons">
  <button class="zoombuttons btn" id="zoom_in">Zoom In</button>
<button class="zoombuttons btn" id="zoom_out">Zoom Out</button>
</div>


    <button class="years btn" id="byyear">By Year</button>
    <button class="years btn" id="allyear">All Years</button>

     
<div id="holder" class="holder" style="color:rgb(113, 119, 83,0.3)"></div>  
</div>
<div class="lejant"><img src="img/lejant.png"></div>
  </div>
</div>





  
    

</div>
<script>
    <?php echo "var deaths_array = $js_array;"; ?>
    
    var parseDates = function(){
        //var parsedate = d3.timeParse("%m-%d-%y").parse;
         
        for(i = 0; i<deaths_array.length;i++){
            let startdate = deaths_array[i].startdate;
            let enddate = deaths_array[i].enddate;
            
            let startyear = startdate.slice(-2);
            let endyear = enddate.slice(-2);
            
            deaths_array[i].startdate = new Date(startdate.substr(0,startdate.length-2) + "19" +startdate.slice(-2));
            deaths_array[i].enddate = new Date(enddate.substr(0,enddate.length-2) + "19" +enddate.slice(-2));
        }
    }
    
    parseDates();
    console.log(deaths_array);
    
    var width  = 1645,
    height = 1000;
    
    var zoom = d3.zoom().scaleExtent([1, 13]).translateExtent([[0, 0], [width, height]]).on("zoom", zoomed);
    
    var svg = d3.select("#map-holder").append("svg")
    .attr("width", width)
    .attr("height",height)
    .call(zoom);
    
    var g = svg.append("g");
    
        
    
     var projection = d3.geoEquirectangular()
    .center([0,10])
      .scale(330)
    .translate([750,500]);
    
   
    
    var path = d3.geoPath()
    .projection(projection);
    
    
    
    
    var formatTime = d3.timeFormat("%B %d, %Y");
    
    var div = d3.select("body").append("div")	
    .attr("class", "tooltip")				
    .style("opacity", 0);
    
    
     var drawCircle = function(d)
     {
        var radius = Math.sqrt( 0.0005 * Math.abs(d.deaths) );
        if (radius<1.5){
            radius = 1.5;
        }
        
        var formatTime = d3.timeFormat("%B %d, %Y");
         
        var projected = projection([ d.lon , d.lat]);
         
        if(d.tags == "The Holocaust" || d.tags == "Atomic Bombing" && d.location =="")
            {
                let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#2C1A1A") //7C4C4C
                             .attr("stroke", "white")   
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.8")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" + "</br>" + "<strong>" + d.tags + "</strong>" + "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths")	
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 28) + "px");	
                                            })					
                                        .on("mouseout", function(d) {
                                        circle.attr("stroke", "white").attr("stroke-width", "0.10");
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                        });
            } 
         
        else if(d.tags == "The Holocaust" || d.tags == "Atomic Bombing")
            {
                let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#2C1A1A") //7C4C4C
                             .attr("stroke", "white")   
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.8")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" + "</br>" + "<strong>" + d.tags + "</strong>" + "<br/>" + d.location + "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths")	
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 28) + "px");	
                                            })					
                                        .on("mouseout", function(d) {
                                        circle.attr("stroke", "white").attr("stroke-width", "0.10");
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                        });
            }
         
        else if(d.location == "" && d.civilianrate =="0")
            {
                let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#7A5F53") //7C4C4C
                             .attr("stroke", "white")   
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.8")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" +  "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths" + "</b>"  + "</br>" + d.tags + " " + "<b>" + d.airforce + "</b>")	
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 28) + "px");	
                                            })					
                                        .on("mouseout", function(d) {
                                        circle.attr("stroke", "white").attr("stroke-width", "0.10");
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                        });
            }
         
         else if(d.civilianrate == "0"){
             let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#7A5F53")
                             .attr("stroke", "white")
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.9")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" +  "<br/>" + d.location + "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths" + "</b>" + "</br>" + d.tags + " " + "<b>" + d.airforce + "</b>")	
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 28) + "px");	
                                            })					
                                        .on("mouseout", function(d) {
                                        circle.attr("stroke", "white").attr("stroke-width", "0.10");
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                        });
         }
         
         else if(d.civilianrate != "0" && d.location != ""){
             let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#B3B4A1")
                             .attr("stroke", "white")
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.7")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" +  "<br/>" + d.location + "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths" + "</b>" + "</br>" + d.tags + " " + "<b>" + d.airforce + "</b>")	
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 28) + "px");	
                                            })					
                                        .on("mouseout", function(d) {
                                        circle.attr("stroke", "white").attr("stroke-width", "0.10");
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                            });
         }
         
          else{
             let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#B3B4A1")
                             .attr("stroke", "white")
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.7")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" + "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths" + "</b>" + "</br>" + d.tags + " " + "<b>" + d.airforce + "</b>")	
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 28) + "px");	
                                            })					
                                        .on("mouseout", function(d) {
                                        circle.attr("stroke", "white").attr("stroke-width", "0.10");
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                        });
         }
        
     };
    
    
     var showData = function(data){
         g.selectAll("circle").remove();
         if(typeof data !== "undefined")
            { 
                for(i=0;i<data.length;i++){
                        drawCircle(data[i]);
                    }      
            }
        
    };
 
     var currentYear =1938;
    
    var getvalue = function(d){
        var parseNum = d3.format("d");
        var year =parseNum(d.value());
        currentYear = year;
        if(typeof year !== "undefined"){
        var newData = deaths_array.filter(function(deaths_array){
            let enddate = deaths_array.enddate;
            return enddate.getFullYear() <= year;
        });
        showData(newData);
            }
    };
    
   
    
     var getvalueYear = function(d){
        var parseNum = d3.format("d");
        var year =parseNum(d.value());
        currentYear = year;
        if(typeof year !== "undefined"){
        var newData = deaths_array.filter(function(deaths_array){
            let enddate = deaths_array.enddate;
            return enddate.getFullYear() == year;
        });
        showData(newData);
            }
    };

    
    function zoomed() {
   g.attr("transform", d3.event.transform);
}
    
    const slider1 = sliderFactory();
    var slideholder = d3.select('#holder');
    
    
    var loadSlider = function(){
        //slider
        slideholder.call(slider1
        .height(72)
        .margin({top: 38, right: 35, bottom: 35, left: 35  })
        .value(1938)
        .ticks(1)
        .scale(true)
        .range([1938,1945])
        .label(true)
        .dragHandler(function(d){getvalue(d);})
       
        );
        
    }

// load and display the World
d3.json("lib/wmap_geo.json", function(json) {
    g.selectAll("path")
      .data(json.features)
    .enter()
      .append("path")
      .attr("d", path)
    
    g.selectAll("path").attr("fill", "#FAFFD4");
    //#FCF1F1
    
    loadSlider();
});
    
    //.dragHandler(function(d){getvalue(d);})
    
function zoomClick() {
    let direction = (this.id === 'zoom_in') ? 1.5 : 0.5;
    zoom.scaleBy(svg.transition().duration(750), direction);

}

d3.select("body").selectAll(".zoombuttons").on('click', zoomClick);

    
    var yearOrder = function(){
        let yearorder = (this.id ==='byyear') ? 1 : 2;
        if(yearorder == 1)
            {
                var newData = deaths_array.filter(function(deaths_array){
                let enddate = deaths_array.enddate;
                return enddate.getFullYear() == currentYear;
                    });
                showData(newData);
                slideholder.call(slider1.dragHandler(function(d){getvalueYear(d);}));
            }
        else
            {
                var newData = deaths_array.filter(function(deaths_array){
                let enddate = deaths_array.enddate;
                return enddate.getFullYear() <= currentYear;
                    });
                showData(newData);
                slideholder.call(slider1.dragHandler(function(d){getvalue(d);}));
            }
    }
d3.select("body").selectAll(".years").on('click', yearOrder);
    
    d3.select("#reset")
    .on("click", resetted);
    
    function resetted() {
  svg.transition()
      .duration(750)
      .call(zoom.transform, d3.zoomIdentity);
}
    
    
 
</script>
 
   
</body>
</html>
