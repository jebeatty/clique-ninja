<?php

$password="test password";
$salt="jfsh38jfd9sk9";
$saltedPassword=$salt.$password;

echo hash('sha256',$password);
echo "<br>";
echo hash('sha256',$salt);
echo "<br>";
echo hash('sha256',$saltedPassword);

?>