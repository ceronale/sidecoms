<?php
ini_set("session.cookie_lifetime",7200);
ini_set("session.gc_maxlifetime",7200); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//ini_set("session.save_path","/tmp");
//session_cache_expire(7200);
session_start();
//session_regenerate_id(true);
if(!isset($_SESSION['user_session'])){ 
  header("Location: ../login/"); 
 }