<?php
include_once('./header.php');
?>
<script>
function makeID(){ 
	return '_' + Math.random().toString(36).substr(2, 9);
}
</script>
<?php
if(!isset($_POST['type'])){
	echo 'select an election type';
}
else{
	$contents = explode(',',file_get_contents($_FILES['file']['tmp_name']));
	echo '<h3>'.$_POST['type'].'</h3>';
	switch($_POST['type']){
		case "runoff":
		case "borda":
		case "approval":
?>
Title: <input id="title"><br><br>
<button onClick="add()">add election</button>
<div id="questions">
	<div style="background-color:#EEE; display:table; margin:5px; padding:5px;" class="poll" id="first">
		election: <input class="question"><br><br>
		<div class="options">
			<button class="addOption">add option</button><br>
			<div style="margin:2px" class="option" id="firstOption">option: <input class="optionText"><button class="delete">delete</button></div>
			<div style="margin:2px" class="option">option: <input class="optionText"><button class="delete">delete</button></div>
		</div>
		<br>		
		<button class="remove">remove election</button>
	</div>
</div>
<button onClick="create()">create poll</button>
<script>
$('.delete').click(function(event){
	$(event.target).parent().remove();
});
$('.remove').click(function(event){
	$(event.target).parent().remove();
});
var template=$("#first").clone(true,true);
template.attr("id","notFirst");
function add(){
	template.clone(true,true).appendTo("#questions");
}
$(".addOption").click(function(event){
	optionTemplate.clone(true,true).appendTo($(event.target).parent());
});
var optionTemplate=$("#firstOption").clone(true,true);
optionTemplate.attr("id","notFirstOption");

function create(){
	var polls = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls');
	var newPoll=Object();
	newPoll.type=<?php echo "'".$_POST['type']."'"?>;
	newPoll.title=$("#title").val();
	newPoll.started=false;
	newPoll.complete=false;
	newPoll.questions=Array();
	newPoll.respondents=Object();
	$(".poll").each(function(index,element){
		var question=Object();
		question.title=$(element).find('.question').val();
		question.options=Array();
		$(element).find('.option').each(function(index,optionElem){
			question.options.push($(optionElem).find('.optionText').val())
		});
		newPoll.questions.push(question);
	});
	var emailList=JSON.parse(<?php echo "'".json_encode($contents)."'";?>);
	for(var email in emailList){
		var emailObj=Object();
		emailObj.responded=false;
		emailObj.id=makeID();
		newPoll.respondents[emailList[email].replace(/\./g,"|")]=emailObj;
	}
	console.log(JSON.stringify(newPoll));
	polls.push(newPoll, function(){
		window.location.href="./elections.php";
	});
}
</script>
<?php
			break;
		case "referendum":
?>
Title: <input id="title"><br><br>
<button onClick="add()">add question</button>
<div id="questions">
	<div style="background-color:#EEE; display:table; margin:5px; padding:5px;" class="ref" id="first">
		question: <input class="question">
		<button class="remove">remove</button>
	</div>
</div>
<button onClick="create()">create poll</button>
<script>
$('.remove').click(function(event){
	$(event.target).parent().remove();
});
var template=$("#first").clone(true,true);
template.attr("id","notFirst");

function add(){
	template.clone(true,true).appendTo("#questions");
}

function create(){
	var polls = new Firebase('https://boiling-torch-3076.firebaseio.com/'+user+'/polls');
	var newPoll=Object();
	newPoll.type="referendum";
	newPoll.started=false;
	newPoll.complete=false;
	newPoll.title=$("#title").val();
	newPoll.questions=Array();
	newPoll.respondents=Object();
	$(".ref").each(function(index,element){
		newPoll.questions.push($(this).find('.question').val());
	});
	var emailList=JSON.parse(<?php echo "'".json_encode($contents)."'";?>);
	for(var email in emailList){
		var emailObj=Object();
		emailObj.responded=false;
		emailObj.id=makeID();
		newPoll.respondents[emailList[email].replace(/\./g,"|")]=emailObj;
	}
	console.log(JSON.stringify(newPoll));
	polls.push(newPoll, function(){
		window.location.href="./elections.php";
	});
}
</script>
<?php
			break;
		default:
			echo 'invalid type';
	}
}
?>