<?php

// $Header: /cvsroot/bitweaver/_bit_kernel/admin/Attic/admin_modules_inc.php,v 1.10 2006/02/08 21:51:14 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once( '../../bit_setup_inc.php' );

global $userlib;

$gBitSystem->verifyPermission( 'bit_p_admin' );

$gBitSmarty->assign('PHP_SELF',$_SERVER['PHP_SELF']);

// module features
$formModuleFeatures = array(
	'feature_collapsible_modules' => array(
		'label' => 'Collapsible Modules',
		'note' => 'This allows users to collapse modules by clicking on their titles. Can be useful if you use many modules.',
	),
	'modallgroups' => array(
		'label' => 'Display modules to all groups always',
		'note' => 'If you activate this, any modules you assign will be visible to all users, regardless of the settings on the layout page.<br />Hint: If you lose your login module, use /users/login.php to login!',
	),
	'feature_modulecontrols' => array(
		'label' => 'Show Module Controls',
		'note' => 'Displays module control buttons at the top of modules for easy placement by users.',
	),
);
$gBitSmarty->assign( 'formModuleFeatures',$formModuleFeatures );

$all_mods = $gBitThemes->getAllModules();                           // Get all column modules (e.g. left & right)
$gBitSmarty->assign_by_ref( 'all_modules', $all_mods );

$all_centers = $gBitThemes->getAllModules( 'templates', 'center_' );   // Get all center templates
$gBitSmarty->assign_by_ref( 'all_centers', $all_centers );

$all_mods = array_merge_recursive($all_mods,$all_centers);                         // Merge them all back into one array

// this wierdness is due to changes in getAllModules() to create a nicer layout. it's needed to keep the js working...
$all_modules = array();
foreach( $all_mods as $pkg => $mods ) {
	foreach( $mods as $key => $mod ) {
		$mods[$key] = $pkg.' -> '.$mod;
	}
	$all_modules = array_merge( $all_modules, $mods );
}
asort( $all_modules );

$actionSummary = array();
$actionsTaken = false;

$processForm = set_tab();

if( $processForm == 'Modules' ) {
	foreach( array_keys( $formModuleFeatures ) as $item ) {
		simple_set_toggle( $item, THEMES_PKG_NAME );
	}
} elseif ( $processForm == 'Edit' ) {
	$modCount = 0;
	foreach ($_REQUEST['fModuleAction'] as $moduleName=>$action) {
		if ($action == 'enable') {
			if (strlen($_REQUEST['fModuleGroups'.$modCount]) > 0) {
				$groupList = preg_replace('/,/',' ',$_REQUEST['fModuleGroups'.$modCount]);
			} else {
				$groupList = NULL;
			}
			$storeMod = array('module_rsrc'=>$moduleName, 'rows'=>$_REQUEST['fModuleRows'.$modCount], 'params'=>$_REQUEST['fModuleParams'.$modCount], 'cache_time'=>$_REQUEST['fModuleCacheTime'.$modCount], 'groups'=>$groupList);
			$gBitThemes->storeModule($storeMod);
			$actionDescr = array('actionType'=>'enable', 'moduleName'=>$moduleName);
			array_push($actionSummary,$actionDescr);
			$actionsTaken = true;
		} elseif ($action == 'disable') {
			$gBitThemes->disableModule($moduleName);
			$actionDescr = array('actionType'=>'disable', 'moduleName'=>$moduleName);
			array_push($actionSummary,$actionDescr);
			$actionsTaken = true;
		}
		$modCount++;
	}
}
$gBitSmarty->assign_by_ref('actionSummary',$actionSummary);
$gBitSmarty->assign_by_ref('actionsTaken',$actionsTaken);


$all_avail_modules = $gBitThemes->getAssignableModules();
$avail_columns = $all_avail_modules['border'];
$avail_centers = $all_avail_modules['center'];
$avail_modules = array_merge($avail_columns , $avail_centers);
//pvd($avail_centers);
//pvd($avail_modules);
$availHash = array(count($all_modules));            // availHash is an array with enough room to store the settings for all modules/templates. 
$gBitSmarty->assign_by_ref('availHash',$availHash);

$groups = $gBitUser->getAllUserGroups( ROOT_USER_ID );
$gBitSmarty->assign_by_ref('groups',$groups);

$modCount = 0;
foreach ($all_modules as $name=>$module) {
    $curMod = &$availHash[$modCount];
    $curMod = array();
    // Fill default values (for those modules not yet activated)
    $curMod['name'] = $module;
    $curMod['module_rsrc'] = $name;
    $curMod['enabled'] = false;
    $curMod['rows'] = '';
    $curMod['cache_time'] = '';
    $curMod['params'] = '';
    $curMod['groups'] = array();
    
    foreach($avail_modules as $availMod) {        
        if ($availMod['module_rsrc'] == $name) {
			// Fill specific information for those modules who are already in use or enabled for use.
			$curMod['enabled'] = true;
			$curMod['rows'] = $availMod['rows'];
			$curMod['cache_time'] = $availMod['cache_time'];
			$curMod['params'] = $availMod['params'];
			$curMod['groups'] = $availMod['groups'];
        }
    }   
    $modCount++;
}

// Get a list of all modules currently used by the kernel for layouts
$query = "SELECT tl.`module_id`, tl.`layout`, tmm.`module_rsrc` FROM `".BIT_DB_PREFIX."themes_layouts` tl, `".BIT_DB_PREFIX."themes_module_map` tmm
	  WHERE tl.`module_id` = tmm.`module_id` AND tl.`user_id` = 1";
$result = $gBitThemes->mDb->query($query);
$rows = $result->getRows();

// Set up the javascript
$javascript = '<script type="text/javascript"><!--';
$javascript .= "var selectedOptionValue = null; var selectedOptionText = null;\n";
$javascript .= "var layoutModules = [";
foreach ($rows as $index=>$row) {
	$javascript .= "'".$row['module_rsrc']."'";
	if ($index < count($rows)-1)
		$javascript .= ',';
}
$javascript .= "];\n";

$javascript .= "function CModule(name, module_rsrc, bEnabled, rows, params, cacheTime) {  this.name = name; this.module_rsrc = module_rsrc; this.bEnabled = bEnabled; this.rows = rows; this.params = params; this.cacheTime = cacheTime; this.groups = new Array(".count($groups).");}\n";
// Store all module data in our javascrip CModule class array
$javascript .= 'modArray = new Array('.count($all_modules).");\n";
foreach ($availHash as $index=>$colMod) {
	$javascript .= "modArray[$index] = new CModule('".$colMod['name']."','".$colMod['module_rsrc']."',".($colMod['enabled'] ? 'true' : 'false').",'".$colMod['rows']."','".$colMod['params']."','".$colMod['cache_time']."');\n";
    if( !empty( $colMod['groups'] ) && count( $colMod['groups'] ) ) {
        $javascript .= "modArray[$index].groups = [ ";
        foreach( $colMod['groups'] as $index=>$group_name) {
            $javascript .= "'$group_name'";
            if ($index < count($colMod['groups'])-1)
                $javascript .= ',';
            else
                $javascript .= "];\n";
        }
    }
}

$javascript .= "var groups = Array(".count($groups).");\n";
$i = 0;
foreach( array_keys( $groups ) as $groupId ) {
    $javascript .= "groups[".$i++."] = '".$groupId."';\n";
}

$javascript .= "function Initialize() {\n";
$javascript .= "    var selectControl = document.getElementById('moduleSelectId');\n";
$javascript .= "    var selIndex = selectControl.selectedIndex;\n";
$javascript .= "    var statusText = '';\n";
$javascript .= "    if (modArray[selIndex].bEnabled == true) { statusText = 'Enabled'; statusColor = '#008800'; } else { statusText = 'Disabled'; statusColor = '#ff0000'; }\n";
$javascript .= "    for (var modCount=0; modCount < selectControl.options.length; modCount++) {\n";
$javascript .= "        if (modArray[modCount].bEnabled) selectControl.options[modCount].style.backgroundColor = '#008800'; else selectControl.options[modCount].style.backgroundColor = '#aa0000';\n";
$javascript .= "        document.getElementById('fModuleRows'+modCount).value = modArray[modCount].rows;\n";
$javascript .= "        document.getElementById('fModuleParams'+modCount).value = modArray[modCount].params;\n";
$javascript .= "        document.getElementById('fModuleCacheTime'+modCount).value = modArray[modCount].cacheTime;\n";
$javascript .= "    }\n";
$javascript .= "    document.getElementById('moduleStatusText').value = statusText;\n";
$javascript .= "    document.getElementById('moduleStatusText').style.color = statusColor;\n";
$javascript .= "}\n";

$javascript .= "function SaveGroupSettings(mod_id) { \n";
$javascript .= "    var groupsStr = '';\n";
$javascript .= "    modArray[mod_id].groups = new Array();\n";
$javascript .= "    var numGroupsFound = 0;\n";
$javascript .= "    for (var groupCount = 0; groupCount < groups.length; groupCount++) {\n";
$javascript .= "        if (document.getElementById('fGroup'+groupCount).selected == true) {\n";
$javascript .= "            groupsStr += groups[groupCount]+',';\n";
$javascript .= "            modArray[mod_id].groups.push(groups[groupCount]);\n";
$javascript .= "            numGroupsFound++;\n";
$javascript .= "        }\n";
$javascript .= "    }\n";
$javascript .= "    var tempStr = new String(groupsStr);\n";
$javascript .= "    if (numGroupsFound > 0)\n";
$javascript .= "        groupsStr = tempStr.substr(0,tempStr.length-1);";
$javascript .= "    document.getElementById('fModuleGroups'+mod_id).value = groupsStr;\n";
$javascript .= "}\n";

$javascript .= "function LoadGroupSettings(mod_id) {\n";
$javascript .= "    var selIndex = mod_id;\n";
$javascript .= "    for (var grpCount = 0; grpCount < groups.length; grpCount++) {\n";
$javascript .= "        var bGroupFound = false;\n";
$javascript .= "        for (var modGrpCount = 0; modGrpCount < modArray[selIndex].groups.length; modGrpCount++) {\n";
$javascript .= "            if (modArray[selIndex].groups[modGrpCount] == groups[grpCount]) {\n";
$javascript .= "                document.getElementById('fGroup'+grpCount).selected = true;\n";
$javascript .= "                bGroupFound = true;\n";
$javascript .= "            }\n";
$javascript .= "        }\n";
$javascript .= "        if (!bGroupFound)\n";
$javascript .= "            document.getElementById('fGroup'+grpCount).selected = false\n";
$javascript .= "    }\n";
$javascript .= "}\n";

$javascript .= "function UpdateModuleWidget(what) {\n";
$javascript .= "    var statusText = '';\n";
$javascript .= "    var selIndex = what.selectedIndex;\n";
$javascript .= "    var preIndex = document.getElementById('fPreIndex').value;\n";
$javascript .= "    modArray[preIndex].rows = document.getElementById('fRows').value\n";
$javascript .= "    modArray[preIndex].cacheTime = document.getElementById('fCacheTime').value\n";
$javascript .= "    modArray[preIndex].params = document.getElementById('fParams').value\n";
$javascript .= "    SaveGroupSettings(preIndex);\n";
$javascript .= "    document.getElementById('fParams').value = modArray[selIndex].params;\n";
$javascript .= "    document.getElementById('fCacheTime').value = modArray[selIndex].cacheTime;\n";
$javascript .= "    document.getElementById('fRows').value = modArray[selIndex].rows;\n";
$javascript .= "    document.getElementById('fPreIndex').value = selIndex;\n";
$javascript .= "    LoadGroupSettings(selIndex)\n";
$javascript .= "    if (modArray[selIndex].bEnabled == true) statusText = 'Enabled'; else statusText = 'Disabled';\n";
$javascript .= "    selectedOptionValue = what.options[selIndex].value;\n";
$javascript .= "    selectedOptionText = what.options[selIndex].text;\n";
$javascript .= "    document.getElementById('moduleStatusText').value = statusText;\n";
$javascript .= "    Initialize();\n";
$javascript .= "}\n";

$javascript .= "function InArray(arr, element) {\n";
$javascript .= "	for (var counter = 0; counter < arr.length; counter++) {\n";
$javascript .= "		if (arr[counter] == element)\n";
$javascript .= "			return true;\n";
$javascript .= "	}\n";
$javascript .= "	return false;\n";
$javascript .= "}\n";

$javascript .= "function ToggleModule() {\n";
$javascript .= "    var modID = document.getElementById('moduleSelectId').selectedIndex;\n";
$javascript .= "    var actionValue = '';\n";
$javascript .= "    if (modArray[modID].bEnabled) {\n";
$javascript .= "	if (InArray(layoutModules,modArray[modID].module_rsrc)) {\n";
$javascript .= "		if (!confirm('This module is currently assigned to a layout. Are you sure you want to disable it?'))\n";
$javascript .= "			return;\n";
$javascript .= "	}\n";
$javascript .= "	actionValue = 'disable';\n";
$javascript .= "    } else {\n";
$javascript .= " 	actionValue = 'enable';\n";
$javascript .= "    }\n";
$javascript .= "    modArray[modID].bEnabled = !modArray[modID].bEnabled;\n";
$javascript .= "    document.getElementById('fModuleAction'+modID).value = actionValue;\n";
$javascript .= "    UpdateModuleWidget(document.getElementById('moduleSelectId'));\n";
$javascript .= "    \n";
$javascript .= "}\n";

$javascript .= "function OnTextChange() {\n";
$javascript .= "    var modID = document.getElementById('moduleSelectId').selectedIndex;\n";
$javascript .= "    if (modArray[modID].bEnabled) document.getElementById('fModuleAction'+modID).value = 'enable';\n";
$javascript .= "}\n";

$javascript .= "function PostSubmitProcess() {\n";
$javascript .= "    var modID = document.getElementById('moduleSelectId').selectedIndex;\n";
$javascript .= "    modArray[modID].rows = document.getElementById('fRows').value;";
$javascript .= "    modArray[modID].cacheTime = document.getElementById('fCacheTime').value;";
$javascript .= "    modArray[modID].params = document.getElementById('fParams').value;";
$javascript .= "    SaveGroupSettings(modID);\n";
$javascript .= "    for (var modCount=0; modCount < modArray.length; modCount++) {\n";
$javascript .= "        document.getElementById('fModuleRows'+modCount).value = modArray[modCount].rows\n";
$javascript .= "        document.getElementById('fModuleParams'+modCount).value = modArray[modCount].params\n";
$javascript .= "        document.getElementById('fModuleCacheTime'+modCount).value = modArray[modCount].cacheTime\n";
$javascript .= "    }\n";
$javascript .= "    return true;\n";
$javascript .= "}\n";
$javascript .= '--></script>';

$gBitSmarty->assign( 'moduleJavascript', $javascript ); 



?>
