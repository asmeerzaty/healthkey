<?php
// PDO singleton + helper: q($sql,$params) always uses prepared statements.
function db(){static $p;if(!$p){$p=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}return $p;}
function q($sql,$a=[]){$s=db()->prepare($sql);$s->execute($a);return $s;}
