<?php
$motDePasse = 'admin';
$hash = password_hash($motDePasse, PASSWORD_DEFAULT);
echo $hash;