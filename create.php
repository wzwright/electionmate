<?php
include_once('./header.php');
?>
<form method="post" action="createType.php" enctype="multipart/form-data">
	Election type: 
	<select name="type" id="type">
		<option value="runoff" data-desc="Rank candidates in order. If no majority is reached, candidates are eliminated until one candidate has a majority">runoff election</option>
		<option value="borda" data-desc="Rank candidates in order. Candidates are awarded a point for each other candidate they are ranked above. Selects the most generally acceptable candidate">borda count</option>
		<option value="approval" data-desc="Select single candidate, most votes wins">highest approval</option>
		<option value="referendum" data-desc="Select yes, no, or abstain">referendum</option>
	</select>
	<br><br>
	<div id="description">
		Rank candidates in order. If no majority is reached, candidates are eliminated until one candidate has a majority
	</div>
	<br>
	recipients csv
	<input type="file" name="file" id="file"><br><br>
	<input type="submit" value="Continue" name="submit">
</form>
<script>
$('#type').on('change', function (e) {
    var selected = $("option:selected", this);
    $('#description').text(selected.attr("data-desc"));
});
</script>