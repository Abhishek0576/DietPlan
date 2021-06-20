<?php

error_reporting(E_ERROR | E_PARSE);

$ageGroup = $_POST['ageGroup'];
$wtGroup = $_POST['weightGroup'];
$cal = $_POST['BMR']; 
$foodtype = $_POST['type'];

if(!isset($cal) && !isset($ageGroup) && !isset($wtGroup) && !isset($foodtype)) 
{
    echo "<html> <head> <title> Diet Plan </title> </head>";
    echo "<body style='background: #eee'>";
    echo "<center>";
    echo "<br>";
    echo "<h2> \"Diet Plan Recommendation System\" </h2> <br><br>"; 
    echo "<label> Click below button to download <b> Diet Plan Zip </b> file and extract apk </label>";
    echo "<h1> <button> <a href='/DietPlan.zip'> <img src='/download-apk.png' /> </a> </button> </h1>";
    echo "</center>";
    echo "</body>";
    echo "</html>";
    exit();
}

$cal = $cal - 175;
$scal = 175;

$bfVegCatList    = array('Dairy products', 'Fruits' , 'Seeds and Nuts', 'Breads, cereals, fastfood,grains' );
$bfNonVegCatList = array('Dairy products', 'Eggs',  'Fruits', 'Seeds and Nuts', 'Breads, cereals, fastfood,grains' );  
$lVegCatList     = array('Breads, cereals, fastfood,grains', 'Soups', 'Dairy products', 'Vegetables' );
$lNonVegCatList  = array('Breads, cereals, fastfood,grains', 'Soups', 'Dairy products', 'Meat, Poultry', 'Fish, Seafood', 'Eggs','Vegetables' );
$sVegCatList     = array('Breads, cereals, fastfood,grains', 'Drinks,Alcohol, Beverages', 'Fruits', 'Desserts, sweets', 'Dairy products', 'Vegetables' );
$sNonVegCatList  = array('Breads, cereals, fastfood,grains', 'Eggs', 'Drinks,Alcohol, Beverages', 'Fruits', 'Desserts, sweets', 'Dairy products', 'Vegetables' );
$dVegCatList     = array('Breads, cereals, fastfood,grains', 'Soups', 'Vegetables', 'Desserts, sweets' );
$dNonVegCatList = array('Breads, cereals, fastfood,grains', 'Soups', 'Meat, Poultry', 'Vegetables', 'Desserts, sweets' );


function intersection($list1, $list2) 
{
    return (array_intersect($list1, $list2));
}


function printList($list)
{
    foreach($list as $value) 
    {
        echo "$value <br>";
    }
}


function getFoodList($table)
{
    global $foodtype;
    global $ageGroup;
    global $wtGroup;
    global $cal; 

    $handle = fopen($table, "r");
    
    $underwtFoods = array();
    $healthyFoods = array();
    $overwtFoods  = array();

    $age_grp1Foods = array();
    $age_grp2Foods = array();
    $age_grp3Foods = array();

    $row = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
    {
        // $size = count($data);
        if($row != 0)
        {
           $foodItem = $data[0]; 
           $consumedBy = $data[9]; 
           $ageGroup1 = $data[10]; 
           $ageGroup2 = $data[11]; 
           $ageGroup3 = $data[12];

           if(strpos($consumedBy,"underweight") !== false)  array_push($underwtFoods, $foodItem); 
           if(strpos($consumedBy,"healthy") !== false)      array_push($healthyFoods, $foodItem); 
           if(strpos($consumedBy,"overweight") !== false)   array_push($overwtFoods, $foodItem); 

           if(strcmp("yes",$ageGroup1) == 0)  array_push($age_grp1Foods, $foodItem); 
           if(strcmp("yes",$ageGroup2) == 0)  array_push($age_grp2Foods, $foodItem); 
           if(strcmp("yes",$ageGroup3) == 0)  array_push($age_grp3Foods, $foodItem);  

        }
        $row += 1;   
    }

    $underwt_agegrp1Foods = intersection($underwtFoods,$age_grp1Foods);
    $healthy_agegrp1Foods = intersection($healthyFoods,$age_grp1Foods);
    $overwt_agegrp1Foods  = intersection($overwtFoods ,$age_grp1Foods);
    
    $underwt_agegrp2Foods = intersection($underwtFoods,$age_grp2Foods);
    $healthy_agegrp2Foods = intersection($healthyFoods,$age_grp2Foods);
    $overwt_agegrp2Foods  = intersection($overwtFoods ,$age_grp2Foods);
    
    $underwt_agegrp3Foods = intersection($underwtFoods,$age_grp3Foods);
    $healthy_agegrp3Foods = intersection($healthyFoods,$age_grp3Foods);
    $overwt_agegrp3Foods  = intersection($overwtFoods ,$age_grp3Foods);

    $Foodlist = '';

    if(strcmp($ageGroup,"AgeGroup-1(20-39)") == 0)
    {
        if(strcmp($wtGroup, "Underweight") == 0)     $Foodlist = $underwt_agegrp1Foods;
        else if(strcmp($wtGroup, "Normal") == 0)     $Foodlist = $healthy_agegrp1Foods;
        else if(strcmp($wtGroup, "Overweight") == 0) $Foodlist = $overwt_agegrp1Foods;
    }

    else if(strcmp($ageGroup,"AgeGroup-2(40-59)") == 0)
    {
        if(strcmp($wtGroup, "Underweight") == 0)     $Foodlist = $underwt_agegrp2Foods;
        else if(strcmp($wtGroup, "Normal") == 0)     $Foodlist = $healthy_agegrp2Foods;  
        else if(strcmp($wtGroup, "Overweight") == 0) $Foodlist = $overwt_agegrp2Foods;
    }

    else if(strcmp($ageGroup,"AgeGroup-3(60-more)") == 0)
    {
        if(strcmp($wtGroup, "Underweight") == 0)     $Foodlist = $underwt_agegrp3Foods;
        else if(strcmp($wtGroup, "Normal") == 0)     $Foodlist = $healthy_agegrp3Foods;
        else if(strcmp($wtGroup, "Overweight") == 0) $Foodlist = $overwt_agegrp3Foods;
    }

    fclose($handle);
    
    return $Foodlist;
      
}

function processDiet($table, $Foodlist, $vCategory, $nvCategory, $totCal)
{
    global $foodtype;
    
    $session = substr($table,10,-4); 
    $category = '';

    $handle = fopen($table, "r");
    if($foodtype == "veg")
    {
        $category = $vCategory;
    }
    else 
    {
        $category = $nvCategory;
    }

    $reqCal = 0;

    $totCal = (int) $totCal;

    $foodDetails = array();
    $row = 0;

    $row = 0;
    while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
    {
        for($i=0; $i<count($Foodlist); $i++)
        {
            if(strcmp($data[0], $Foodlist[$i]) == 0 && $row != 0)
            {
                $food = $data[0];
                $cal = $data[3];
                $fat= $data[5];
                $protien = $data[4];
                $qty = $data[1];
                $cat = $data[8];   
            
                $temp = array();
                $temp["food"] = $food;
                $temp["cal"] = $cal;
                $temp["fat"] = $fat; 
                $temp["protein"] = $protien;
                $temp["qty"] = $qty;
                $temp["cat"] = $cat;
               
                array_push($foodDetails, $temp);
            }
        }
        $row++;
    }
    

    $dCat = array();
    for($i=0; $i<count($category); $i++)
    {
        $dCat[$category[$i]] = array(); 
    }

    for($i=0; $i<count($foodDetails); $i++)
    {
        $itemDetails = $foodDetails[$i];
        $itemCat = $itemDetails["cat"]; 
        $item = $itemDetails["food"];
        
        array_push($dCat[$itemCat], $item);
    }

    $sesssionFood = array();


    for($i=0; $i<count($dCat); $i++)
    { 
        $catList = ($dCat[$category[$i]]);
        if(count($catList) !== 0 )
        {
            $random_key = array_rand($catList);
            $random_food = $catList[$random_key];
            array_push($sesssionFood, $random_food);
        }
    }


    $sessionDetails = array() ;

    for($i=0; $i<count($foodDetails); $i++)
    {
        for($j=0; $j<count($sesssionFood); $j++)
        {
            $itemDetails = $foodDetails[$i];
            $itemCat = $itemDetails["cat"]; 
            $item = $itemDetails["food"];
            
            if(strcmp($item, $sesssionFood[$j]) == 0)
            {
                $itemCal = (int) $itemDetails["cal"];  
                
                if($reqCal + $itemCal <= $totCal)
                {
                    array_push($sessionDetails, $itemDetails);
                    $reqCal += $itemCal;
                }
                else
                {
                    continue;
                }
            }    
        }
    }

    return $sessionDetails;
}


$breakfast_csv = 'nutrients_breakfast.csv';
$lunch_csv     = 'nutrients_lunch.csv';
$snack_csv     = 'nutrients_snack.csv';
$dinner_csv    = 'nutrients_dinner.csv';

$breakfastFoods = getFoodList($breakfast_csv);
$lunchFoods = getFoodList($lunch_csv);
$snackFoods = getFoodList($snack_csv);
$dinnerFoods = getFoodList($dinner_csv);

$breakfastList = processDiet($breakfast_csv, $breakfastFoods, $bfVegCatList, $bfNonVegCatList, (int)($cal/4));
$lunchList     = processDiet($lunch_csv,     $lunchFoods,     $lVegCatList,  $lNonVegCatList,  (int)($cal/2));
$snackList     = processDiet($snack_csv,     $snackFoods,     $sVegCatList,  $sNonVegCatList,  $scal);
$dinnerList    = processDiet($dinner_csv,    $dinnerFoods,    $dVegCatList,  $dNonVegCatList,  (int)($cal/4));

$dietplan = array();

$dietplan["breakfast"] = $breakfastList;
$dietplan["lunch"] = $lunchList;
$dietplan["snack"] = $snackList;
$dietplan["dinner"] = $dinnerList;

$JSON_Data = json_encode($dietplan);
echo $JSON_Data;


?>
