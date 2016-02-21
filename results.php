<?php
include_once('./header.php');
?>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
var poll = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+<?php echo "'".$_GET['id']."'"?>);
var counter=-1;
var counts=Object();
poll.once('value', function(snapshot){
	var data=snapshot.val()
	$("#title").text(data.title);
	var svg = d3.select("#chart")
			.append("svg")
			.append("g")

		svg.append("g")
			.attr("class", "slices");
		svg.append("g")
			.attr("class", "labels");
		svg.append("g")
			.attr("class", "lines");

		var width = 960,
		    height = 450,
			radius = Math.min(width, height) / 2;

		var pie = d3.layout.pie()
			.sort(null)
			.value(function(d) {
				return d.value;
			});

		var arc = d3.svg.arc()
			.outerRadius(radius * 0.8)
			.innerRadius(radius * 0.4);

		var outerArc = d3.svg.arc()
			.innerRadius(radius * 0.9)
			.outerRadius(radius * 0.9);

		svg.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

		var key = function(d){ return d.data.label; };
function change(data) {

	/* ------- PIE SLICES -------*/
	var slice = svg.select(".slices").selectAll("path.slice")
		.data(pie(data), key);

	slice.enter()
		.insert("path")
		.style("fill", function(d) { return color(d.data.label); })
		.attr("class", "slice");

	slice		
		.transition().duration(1000)
		.attrTween("d", function(d) {
			this._current = this._current || d;
			var interpolate = d3.interpolate(this._current, d);
			this._current = interpolate(0);
			return function(t) {
				return arc(interpolate(t));
			};
		})

		slice.exit()
			.remove();

		/* ------- TEXT LABELS -------*/

		var text = svg.select(".labels").selectAll("text")
			.data(pie(data), key);

		text.enter()
			.append("text")
			.attr("dy", ".35em")
			.text(function(d) {
				return d.data.label;
			});

		function midAngle(d){
			return d.startAngle + (d.endAngle - d.startAngle)/2;
		}

		text.transition().duration(1000)
			.attrTween("transform", function(d) {
				this._current = this._current || d;
				var interpolate = d3.interpolate(this._current, d);
				this._current = interpolate(0);
				return function(t) {
					var d2 = interpolate(t);
					var pos = outerArc.centroid(d2);
					pos[0] = radius * (midAngle(d2) < Math.PI ? 1 : -1);
					return "translate("+ pos +")";
				};
			})
			.styleTween("text-anchor", function(d){
				this._current = this._current || d;
				var interpolate = d3.interpolate(this._current, d);
				this._current = interpolate(0);
				return function(t) {
					var d2 = interpolate(t);
					return midAngle(d2) < Math.PI ? "start":"end";
				};
			});

		text.exit()
			.remove();

		/* ------- SLICE TO TEXT POLYLINES -------*/

		var polyline = svg.select(".lines").selectAll("polyline")
			.data(pie(data), key);

		polyline.enter()
			.append("polyline");

		polyline.transition().duration(1000)
			.attrTween("points", function(d){
				this._current = this._current || d;
				var interpolate = d3.interpolate(this._current, d);
				this._current = interpolate(0);
				return function(t) {
					var d2 = interpolate(t);
					var pos = outerArc.centroid(d2);
					pos[0] = radius * 0.95 * (midAngle(d2) < Math.PI ? 1 : -1);
					return [arc.centroid(d2), outerArc.centroid(d2), pos];
				};			
			});

		polyline.exit()
			.remove();
		};
		d3.select(".next")
			.on("click", function(){
				change(getData());
			});
	if(data.type=="referendum"){
		for(var i=0;i<data.questions.length;i++)
				counts[i]={yes:0,no:0,abstain:0};
			for(var email in data.respondents){
				for(var j=0;j<data.questions.length;j++){
					if(data.respondents[email].responded)
						counts[j][data.respondents[email].votes[j]]++;
				}
			}

		var color = d3.scale.ordinal()
			.domain(["yes","no","abstain"])
			.range(["#1abc9c","#c0392b","#f1c40f",]);

		function getData (){
		counter++;
		$("#question").text(data.questions[counter%data.questions.length]);
		var labels = color.domain();
		var sum=counts[counter%data.questions.length]['yes']+counts[counter%data.questions.length]['no']+counts[counter%data.questions.length]['abstain'];
		var curCounts=counts[counter%data.questions.length];
		$("#votes").html("yes "+curCounts['yes']+"<br> no "+curCounts['no']+"<br> abstain "+curCounts['abstain']);
		return labels.map(function(label){
				return { label: label, value: (counts[counter%data.questions.length][label])/sum }
			});
		}
		change(getData());

		poll.on('value',function(snapshot){
			data=snapshot.val()
			for(var i=0;i<data.questions.length;i++)
				counts[i]={yes:0,no:0,abstain:0};
			for(var email in data.respondents){
				for(var j=0;j<data.questions.length;j++){
					if(data.respondents[email].responded)
						counts[j][data.respondents[email].votes[j]]++;
				}
			}
			change(getData());
		});
}
else if(data.type=="approval"){
	for(var i=0;i<data.questions.length;i++){
		counts[i]=Object();
		for(var option in data.questions[i].options)
			counts[i][option]=0;
	}
	for(var email in data.respondents){
		for(var j=0;j<data.questions.length;j++){
			if(data.respondents[email].responded)
				counts[j][data.respondents[email].votes[j].charAt(0)]++;
		}
	}
	console.log(counts);
	var color = d3.scale.category10();

		function getData (){
		counter++;
		$("#question").text(data.questions[counter%data.questions.length].title);
		var labels = data.questions[counter%data.questions.length].options;
		var sum=0;
		for(val in counts[counter%data.questions.length])
			sum+=counts[counter%data.questions.length][val];
		var curCounts=counts[counter%data.questions.length];
		var text="";
		for(val in counts[counter%data.questions.length])
			text+=data.questions[counter%data.questions.length][val]+" "+counts[counter%data.questions.length][val]+"<br>";
		$("#votes").html(text);
		return labels.map(function(label){
				return { label: label, value: (counts[counter%data.questions.length][label])/sum }
			});
		}
		change(getData());

		poll.on('value',function(snapshot){
			for(var i=0;i<data.questions.length;i++){
		counts[i]=Object();
		for(var option in data.questions[i].options)
			counts[i][option]=0;
	}
	for(var email in data.respondents){
		for(var j=0;j<data.questions.length;j++){
			if(data.respondents[email].responded)
				counts[j][data.respondents[email].votes[j].charAt(0)]++;
		}
	}
			change(getData());
		});

	}
});
</script>
<body>
<div style="width:80%" id="chart">
<h2 id="title"></h2>
<h3 id="question"></h3>
<h4 id="votes"></h4>
</div>
<button style="position:fixed; top:10px; left:600px;" class="next">next</button>
</body>