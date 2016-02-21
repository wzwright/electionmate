<?php
include_once('./header.php');
?>
<html>
<head>
	<title>ElectionM8</title>
	<script>
	window.onload=function(){
		var polls = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls');
		polls.once('value', function(snapshot){
			var data=snapshot.val();
			for(var poll in data){
				var col=(12-12*data[poll].complete).toString(16)+(12*data[poll].started).toString(16)+"1";
				var elem="<a style='text-decoration:none;' href=./election.php?id="+poll+">";
				elem+="<div style='background-color:#"+col+"; color:#EEE; display:table; margin:5px; padding:5px;'><strong>"+data[poll].type+": </strong>"+data[poll].title+"</div></a>";
				$("body").append(elem);
			}
		});
	};
	</script>
</head>
<body>
	<h2>elections</h2>
</body>
</html>