<?php
include_once('./header.php');
?>
<script>
var data;
var id=<?php echo "'".$_GET['id']."'"?>;
window.onload=function(){
	var poll = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+id);
	poll.once('value', function(snapshot){
		data=snapshot.val();
		$("body").append("<h2>"+data.title+"</h2>");
		var type=data.type;
		if(type=="referendum"){
			$("body").append("<h3>referendum</h3>");
			for(var i in data.questions){
				$("body").append("<div>"+data.questions[i]+"</div><br>");
			}
		}
		else{
			$("body").append("<h3>"+type+"</h3>");
			for(var i in data.questions){
				var element="<div><h4>"+data.questions[i].title+"</h4>";
				for(var j in data.questions[i].options){
					element+=data.questions[i].options[j]	+"<br>";
				}
				element+="</div><br>";
				$("body").append(element);
			}
		}
		if(!data.started)
			$("body").append("<button onClick='start()'>Open voting</button>");
		else{ 
			if(!data.complete)
				$("body").append("<button onClick='closePoll()' style='margin-bottom:10px;'>Close voting</button>");
			else
				$("body").append("<h3>This poll is closed</h3>");
			$("body").append("<br><a href='./results.php?id="+id+"'>View results</a>");
		}
	});
};

function start(){
	var started = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+<?php echo "'".$_GET['id']."'"?>+"/started");
	started.set(true,function(){
		window.location.href="./startElection.php?id="+id+"&title="+data.title+"&mailTo="+JSON.stringify(data.respondents);
	});
}
function closePoll(){
	var complete = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+<?php echo "'".$_GET['id']."'"?>+"/complete");
	complete.set(true,function(){
		window.location.href="./results.php?id="+id;
	});	
}

</script>