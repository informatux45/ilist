<?php
// -----------------------------
// ---------- OPTIONS ----------
// ------- WITHOUT GROUP -------
// -----------------------------
$option_one = 0;
$option_two = 1;
$option_three = 1;
// ------
$tab_check = array();
// ------
$tab_check[0]['text']     = 'Option 1';
$tab_check[0]['name']     = 'option_one';
$tab_check[0]['checked']  = ($option_one == 1) ? '1' : '0';
$tab_check[0]['value']    = 'OPT1';
$tab_check[0]['disabled'] = "disabled";
// ------
$tab_check[1]['text']     = 'Option 2';
$tab_check[1]['name']     = 'option_two';
$tab_check[1]['checked']  = ($option_two == 1) ? '1' : '0';
$tab_check[1]['value']    = 'OPT2';
// ------
$tab_check[2]['text']     = 'Option 3';
$tab_check[2]['name']     = 'option_three';
$tab_check[2]['checked']  = ($option_three == 1) ? '1' : '0';
$tab_check[2]['value']    = 'OPT3';
// ------
echo $framework->addCheckbox(
     'Toutes vos options' // Label
    ,$tab_check           // Datas (text, name, checked, value, disabled)
    ,array()              // Args 
    ,false                // Required: true OR false
    ,'<br>'               // Separator
    ,''                   // Description
);

// -----------------------------
// ---------- GROUPED ----------
// -----------------------------
$option_cats  = 1;
$option_dogs  = 0;
$option_birds = 1;
// ------
$tab_check = array();
// ------
$tab_check[0]['text']     = 'Cats';
$tab_check[0]['name']     = 'favorite_pet';
$tab_check[0]['checked']  = ($option_cats == 1) ? '1' : '0';
$tab_check[0]['value']    = 'cats';
//$tab_check[0]['disabled'] = "disabled";
// ------
$tab_check[1]['text']     = 'Dogs';
$tab_check[1]['name']     = 'favorite_pet';
$tab_check[1]['checked']  = ($option_dogs == 1) ? '1' : '0';
$tab_check[1]['value']    = 'dogs';
// ------
$tab_check[2]['text']     = 'Birds';
$tab_check[2]['name']     = 'favorite_pet';
$tab_check[2]['checked']  = ($option_birds == 1) ? '1' : '0';
$tab_check[2]['value']    = 'birds';
// ------
echo $framework->addCheckbox(
     'Toutes vos options' // Label
    ,$tab_check           // Datas (text, name, checked, value, disabled)
    ,array()              // Args 
    ,false                // Required: true OR false
    ,'<br>'               // Separator
    ,''                   // Description
);

?>