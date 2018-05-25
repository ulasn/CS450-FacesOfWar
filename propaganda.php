<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<head>
<title>Faces Of War  </title>


    <script src="lib/d3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body{overflow:hidden;}
        
        #map-holder{
            margin-top:-200px!important;
        }
        div#resetbutton {
            top:200px;
        }
        div#buttons{
            top:172px;
        }
        
        .mark{
            
        }
        svg{
            background:#95A196; 
        }
        
        div.tooltip {
            background:#202020;
            color:white;
        }

        .modal {
            height: auto;
        }

        .modal-dialog {
            position: absolute;
            right: 10px;
            left: auto;
            width: 435px;
        }
        
        #myModal{
            max-height:850px;
        }
    
    </style>
</head>

<body>
   
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      
      <div class="modal-content">
          
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <div class="modal-body" id="modal-body">
          <p>Some text in the modal.</p>
        </div>
        <!--
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        -->
      </div>
      
    </div>
  </div>
    
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
    <center><h3>World War II Propaganda</h3></center>
    <center><p class="card-text">Infographic about World War II Propaganda with propaganda posters and radio broadcasts</p></center><br>   
    
   </div>
  <div class="card-body">
  
  <div id="map-holder">

<div id="resetbutton" class="resetbutton"><button class="btn" id="reset">Reset</button></div>
   <div id="buttons" class="buttons">
  <button class="zoombuttons btn" id="zoom_in">Zoom In</button>
<button class="zoombuttons btn" id="zoom_out">Zoom Out</button>
</div>


     
</div>
  </div>
</div>
<script>
    
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
    
        
    var div = d3.select("body").append("div")	
    .attr("class", "tooltip")				
    .style("opacity", 0);
    
    
    function zoomed() {
   g.attr("transform", d3.event.transform);
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
    
    appendMarks();
});
    
    //.dragHandler(function(d){getvalue(d);})
    
function zoomClick() {
    let direction = (this.id === 'zoom_in') ? 1.5 : 0.5;
    zoom.scaleBy(svg.transition().duration(750), direction);

}

d3.select("body").selectAll(".zoombuttons").on('click', zoomClick);

    

    var appendMarks = function(){
        
        d3.json("json/us_coordinates.json", function(json){
            var mark = g.selectAll("mark")
    .data(json.coordinates)
    .enter()
    .append("image")
    .attr('class','mark')
    .attr('width', 15)
    .attr('height', 15)
    .attr("href",function(d) {return "img/spin.png"}) //"img/usa/"+ d.id +".jpg"
    .attr("transform", function(d) {return "translate(" + projection([d.longitude,d.latitude]) + ")";})
            .on("mouseover", function(d) {
                                                    d3.select(this).attr("border-width", "5px").attr("border-color", "black");
                                                    div.transition()
                                                        .duration(200)		
                                                        .style("opacity", 1);
                                      div.html("<img style=\"max-height:500px;max-width:350px;\" src=\"" + "img/usa/"+ d.id +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>")
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 200) + "px").style("max-width","400px").style("height", "auto");
                                            })					
            .on("mouseout", function(d) {
                                        d3.select(this).attr("stroke", "white").attr("stroke-width", "100")
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                         })
            .on("click", function(d) {
                console.log("clicked at" + d.id);
                $("#myModal").modal('show');

                $("#modal-body").html("");
                
                for(var i=-2; i <= 2; i++ ){  
                    $("#modal-body").append("<img style=\"max-width:400px;\" src=\"" + "img/usa/"+ (d.id + i) +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>");
                }
                
            });
            
        });
        
        d3.json("json/ru_coordinates.json", function(json){
            var mark = g.selectAll("mark")
    .data(json.coordinates)
    .enter()
    .append("image")
    .attr('class','mark')
    .attr('width', 15)
    .attr('height', 15)
    .attr("href",function(d) {return "img/spin.png"}) //"img/usa/"+ d.id +".jpg"
    .attr("transform", function(d) {return "translate(" + projection([parseFloat(d.lng),parseFloat(d.lat)]) + ")";})
            .on("mouseover", function(d) {
                                                    d3.select(this).attr("border-width", "5px").attr("border-color", "black");
                                                    div.transition()
                                                        .duration(200)		
                                                        .style("opacity", 1);
                                      div.html("<img style=\"max-height:500px;max-width:350px;\" src=\"" + "img/russia/"+ d.id +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>")
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 200) + "px").style("max-width","400px").style("height", "auto");
                                            })					
            .on("mouseout", function(d) {
                                        d3.select(this).attr("stroke", "white").attr("stroke-width", "100")
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                         })
            .on("click", function(d) {
                console.log("clicked at" + d.id);
                $("#myModal").modal('show');

                $("#modal-body").html("");
                
                for(var i=-2; i <= 2; i++ ){  
                    $("#modal-body").append("<img style=\"max-width:400px;\" src=\"" + "img/russia/"+ (d.id + i) +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>");
                }
                
            });
            
        });
        
        d3.json("json/ger_coordinates.json", function(json){
            var mark = g.selectAll("mark")
    .data(json.coordinates)
    .enter()
    .append("image")
    .attr('class','mark')
    .attr('width', 10)
    .attr('height', 10)
    .attr("href",function(d) {return "img/spin.png"}) //"img/usa/"+ d.id +".jpg"
    .attr("transform", function(d) {return "translate(" + projection([parseFloat(d.lng),parseFloat(d.lat)]) + ")";})
            .on("mouseover", function(d) {
                                                    d3.select(this).attr("border-width", "5px").attr("border-color", "black");
                                                    div.transition()
                                                        .duration(200)		
                                                        .style("opacity", 1);
                                      div.html("<img style=\"max-height:500px;max-width:350px;\" src=\"" + "img/germany/"+ d.id +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>")
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 200) + "px").style("max-width","400px").style("height", "auto");
                                            })					
            .on("mouseout", function(d) {
                                        d3.select(this).attr("stroke", "white").attr("stroke-width", "100")
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                         })
            .on("click", function(d) {
                console.log("clicked at" + d.id);
                $("#myModal").modal('show');

                $("#modal-body").html("");
                
                for(var i=-2; i <= 2; i++ ){  
                    $("#modal-body").append("<img style=\"max-width:400px;\" src=\"" + "img/germany/"+ (d.id + i) +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>");
                }
                
            });
            
        });
        
        d3.json("json/uk_coordinates.json", function(json){
            var mark = g.selectAll("mark")
    .data(json.coordinates)
    .enter()
    .append("image")
    .attr('class','mark')
    .attr('width', 10)
    .attr('height', 10)
    .attr("href",function(d) {return "img/spin.png"}) //"img/usa/"+ d.id +".jpg"
    .attr("transform", function(d) {return "translate(" + projection([parseFloat(d.lng),parseFloat(d.lat)]) + ")";})
            .on("mouseover", function(d) {
                                                    d3.select(this).attr("border-width", "5px").attr("border-color", "black");
                                                    div.transition()
                                                        .duration(200)		
                                                        .style("opacity", 1);
                                      div.html("<img style=\"max-height:500px;max-width:350px;\" src=\"" + "img/uk/"+ d.id +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>")
                                     .style("left", (d3.event.pageX) + "px")		
                                     .style("top", (d3.event.pageY - 200) + "px").style("max-width","400px").style("height", "auto");
                                            })					
            .on("mouseout", function(d) {
                                        d3.select(this).attr("stroke", "white").attr("stroke-width", "100")
                                        div.transition()		
                                            .duration(500)		
                                            .style("opacity", 0);	
                                         })
            .on("click", function(d) {
                console.log("clicked at" + d.id);
                $("#myModal").modal('show');

                $("#modal-body").html("");
                
                for(var i=-2; i <= 2; i++ ){  
                    $("#modal-body").append("<img style=\"max-width:400px;\" src=\"" + "img/uk/"+ (d.id + i) +".jpg" +"\">" + "</br></br><strong>" + d.poster+ "</strong>");
                }
                
            });
            
        });
         
        
    }

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