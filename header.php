<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
<link href="./default.css" rel="stylesheet">
<?php
session_start();
$_SESSION['user']="winston";
$_SESSION['email']="comprep@magdjcr.co.uk";
?>
<script>
var user=<?php echo '"'.$_SESSION['user'].'"'?>;
</script>