<?php

$selection_table = "";
$sb_tables = array('Selection 1','Selection 2','Selection 3');
echo $framework->openSelect("Votre selection", array("id"=>"selection", "name"=>"selection"));
if (!isset($selection_table) && $selection_table == '') echo $framework->addOption('Choisissez une table', array ("value"=>"", "selected"=>""));
for($i = 0; $i < count($sb_tables); $i++) {
    if ($sb_tables[$i] == $selection_table)
        echo $framework->addOption($sb_tables[$i], array ("value"=>$sb_tables[$i], "selected"=>""));
else
        echo $framework->addOption($sb_tables[$i], array ("value"=>$sb_tables[$i]));
}
// --- Close Select
echo $framework->closeSelect();

?>