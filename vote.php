<?php
include_once('./header.php');
?>
<script>
<?php
echo 'var id="'.$_GET['id'].'";';
//echo 'var user="'.$_GET['owner'].'";';
echo 'var pollName="'.$_GET['poll'].'";';
echo 'var email="'.$_GET['email'].'";';
?>
var data;
var type;
window.onload=function(){
	email=email.replace(/\./g,"|");
	var poll = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+pollName+'/respondents/'+email);
	poll.once('value', function(snapshot){
		data=snapshot.val();
		if(data.id!=id){
			$("body").append("<h1>You must have the proper link to vote</h1>");
			throw "incorrect id";
			return;
		}
		if(data.responded){
			$("body").append("<h1>Thank you for voting!</h1>");
			return;
		}
		poll=poll.parent().parent();
		poll.once('value', function(snapshot){
			data=snapshot.val();
			$("body").append("<h2>"+data.title+"</h2>");
			type=data.type;
			if(type=="referendum"){
				$("body").append("<h3>referendum</h3>");
				for(var i in data.questions){
					var element="<div id='"+i+"'><h4>"+data.questions[i];
					element+='</h4>yes <input type="radio" name="ref'+i+'" value="yes">';
					element+='<br>no <input type="radio" name="ref'+i+'" value="no">';
					element+='<br>abstain <input type="radio" name="ref'+i+'" value="abstain"></div><br>';
					
					$("body").append(element);
				}
			}
			else{
				$("body").append("<h3>Rank the candidates, Higher is better</h3>");
				for(var i in data.questions){
					var q = data.questions[i];
					var element="<div id='"+i+"'><h4>"+q.title+"</h4>";
					var table="<table><tr><th></th>";
					for(var j=1;j<=q.options.length;j++)
						table+="<th>"+j+"</th>";
					table+="</tr>";
					for(var j in q.options){
						table+="<tr><td>"+q.options[j]+"</td>";
						for(var k=1;k<=q.options.length;k++)
							table+="<td><input type='radio' name='"+j+"' data-col='"+k+"'></td>";
						table+="</tr>";
					}
					element+=table;
					element+="</table></div><br>";
					$("body").append(element);
				}
			}
			$("body").append("<button onClick='submit()'>Submit choices</button>");

			var col, el;
			$("input[type=radio]").click(function() {
				el = $(this);
				col = el.data("col");
				$("input[data-col=" + col + "]").prop("checked", false);
				el.prop("checked", true);
			});
		});
	});
};

String.prototype.replaceAt=function(index, character) {
    return this.substr(0, index) + character + this.substr(index+character.length);
}
function submit(){
	if(type=="referendum"){
		var votes=Object();
		for(var i in data.questions){
			votes[i]=$("#"+i).find("input[type='radio']:checked").val();
		}
		var userVotes = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+pollName+'/respondents/'+email+'/votes');
		userVotes.set(votes);
		userVotes.parent().child("responded").set(true, function(){
			location.reload();
		});
	}
	else{
		var votes=Object();
		for(var i in data.questions){
			var voteString="0".repeat(data.questions[i].options.length);
			$("#"+i).find("input[type='radio']:checked").each(function(index,element){
				voteString=voteString.replaceAt(parseInt($(element).attr("name")),$(element).attr("data-col"));
			});
			votes[i]=voteString;
		}
		var userVotes = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls/'+pollName+'/respondents/'+email+'/votes');
		userVotes.set(votes);
		userVotes.parent().child("responded").set(true, function(){
			location.reload();
		});
	}
}
</script>