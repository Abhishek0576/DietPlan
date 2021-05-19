<?php
  //echo "Hello world";
  //echo print_r($_POST);
  
  // /*
  $ageGroup = $_POST['ageGroup'];
  $weightGroup = $_POST['weightGroup'];
  $cal = $_POST['BMR']; 
  $type = $_POST['type'];
  // */

  // echo "\n".$ageGroup."\n".$weightGroup."\n".$cal;

  /*
  $ageGroup = "AgeGroup-1(20-39)";
  $weightGroup = "Normal";
  $cal = "1300";
  $type = "veg";
  */

  $output = shell_exec("python dietplan.py $ageGroup $weightGroup $cal $type");

  echo $output;
   
?>