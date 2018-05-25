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
    
    var width  = 1900,
    height = 800;
    
    var zoom = d3.zoom().scaleExtent([1, 13]).translateExtent([[0, 0], [width, height]]).on("zoom", zoomed);
    
    var svg = d3.select("#map-holder").append("svg")
    .attr("width", width)
    .attr("height",height)
    .call(zoom);
    
    var g = svg.append("g");
    
    
    
     var projection = d3.geoEquirectangular()
    .center([0,10])
      .scale(370)
    .translate([700,450]);
    
   
    
    var path = d3.geoPath()
    .projection(projection);
    
    
    //slider
    const slider1 = sliderFactory();
    let slideholder4 = d3.select('#holder').call(slider1
	.height(105)
	.margin({top: 50, right: 35, bottom: 25, left: 35  })
	.value(1938)
	.ticks(1)
	.scale(true)
	.range([1938,1945])
	.label(true)
    .dragHandler(function(d){getvalue(d);})
	);
    
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
         
        if(d.location == "")
            {
                let circle = g.append("circle")
                             .datum(d)
                             .attr("cx", projected[0])
                             .attr("cy", projected[1])
                             .attr("r", radius)
                             .attr("fill", "#7C4C4C")
                             .attr("stroke", "white")   
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.8")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" +  "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths" + "</b>")	
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
                             .attr("fill", "#5f0000")
                             .attr("stroke", "white")
                             .attr("stroke-width", "0.10")
                             .attr("opacity", "0.9")
                             .on("mouseover", function(d) {	
                                                    circle.attr("stroke", "black").attr("stroke-width", "0.35");
                                                    div.transition()		
                                                        .duration(200)		
                                                        .style("opacity", 1);		
                                        div.html("<strong>" + d.nationality +"</strong>" +  "<br/>" + d.location + "<br/>" + formatTime(d.startdate) +" - "+formatTime(d.enddate) + "<br/>"  + "<b>" +  d.deaths  + " deaths" + "</b>")	
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
 
    
    var getvalue = function(d){
        var parseNum = d3.format("d");
        var year =parseNum(d.value());
        if(typeof year !== "undefined"){
        var newData = deaths_array.filter(function(deaths_array){
            let enddate = deaths_array.enddate;
            return enddate.getFullYear() <= year;
        });
        showData(newData);
            }
    };

    
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
    
    g.selectAll("path").attr("fill", "#FCF1F1");
    //#FCF1F1
});
    
    
    
function zoomClick() {
    let direction = (this.id === 'zoom_in') ? 1.5 : 0.5;
    zoom.scaleBy(svg.transition().duration(750), direction);

}

d3.select("body").selectAll(".zoombuttons").on('click', zoomClick);


    
    d3.select("#reset")
    .on("click", resetted);
    
    function resetted() {
  svg.transition()
      .duration(750)
      .call(zoom.transform, d3.zoomIdentity);
}