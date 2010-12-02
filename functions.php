<?php
require_once "config.php";
require_once "xbmcjsonlib.php";

function to_readable_size($size) {
	switch (true) {
		case ($size > 1000000000000):
			$size /= 1000000000000;
			$suffix = 'Tb';
		break;
		case ($size > 1000000000):  
			$size /= 1000000000;
			$suffix = 'Gb';
		break;
		case ($size > 1000000):
			$size /= 1000000;
			$suffix = 'Mb';   
		break;
		case ($size > 1000):
			$size /= 1000;
			$suffix = 'Kb';
		break;
		default:
		$suffix = 'b';
	}
	return round($size, 0)." ".$suffix;
}
  
function disk_used_space($value) {
	return disk_total_space("$value") - disk_free_space("$value");
}

function disk_used_percentage($value) {
	return round(disk_used_space("$value") / disk_total_space("$value") * 100, 2);
}

function formattimes($input1, $input2) {
	$seconds1 = $input1 % 60;
	$input1 = floor($input1 / 60);
	$minutes1 = $input1 % 60;
	$hours1 = floor($input1 / 60); 

	$seconds2 = $input2 % 60;
	$input2 = floor($input2 / 60);

	$minutes2 = $input2 % 60;
	$hours2 = floor($input2 / 60); 
	
	if($hours1 > 0 || $hours2 > 0) {
		$output1 = str_pad($hours1,2,'0',STR_PAD_LEFT).":";
		$output2 = str_pad($hours2,2,'0',STR_PAD_LEFT).":";
	} else {
		$output1 = "";
		$output2 = "";
	}
	$output1 = $output1.str_pad($minutes1, 2, '0', STR_PAD_LEFT).":".str_pad($seconds1, 2, '0', STR_PAD_LEFT);
	$output2 = $output2.str_pad($minutes2, 2, '0', STR_PAD_LEFT).":".str_pad($seconds2, 2, '0', STR_PAD_LEFT);

	return $output1." - ".$output2;
}
function addSettings($settings) {
		// Open the database
	try {   $db = new PDO('sqlite:settings.db');

		// Create the database if it doesn't exist
		$db->exec("CREATE TABLE Settings (Id TEXT PRIMARY KEY, Label TEXT, Value TEXT)");

		// Add each setting to database
		foreach ($settings as $setting) {
			//echo print_r($Value,1);
			// Prepare the SQL Statement
			$sql = "INSERT INTO Settings (Id, Label, Value) VALUES (:Id, :Label, :Value);";
			$q = $db->prepare($sql);
			$q->execute(array(	':Id'	=>$setting['id'],
						':Label'=>$setting['label'],
						':Value'=>serialize($setting['value'])));
		}

	} catch(PDOException $e) {
		print 'Exception : '.$e->getMessage();
	}

	// Close the database connection
	$db = NULL;
}
function updateSetting($id, $value) {

		// Open the database
	try {	$db = new PDO('sqlite:settings.db');

		// Replace value in specified column for this widget
		$request = $db->prepare("UPDATE Settings SET Value='".serialize($value)."' WHERE Id='".$id."'");
		$request->execute();

		// Close the database connection
		$db = null;

	} catch(PDOException $e) {
		print 'Exception : '.$e->getMessage();
	}
}
function getAllSettings() {
		// Open the database
	try {	$db = new PDO('sqlite:settings.db');

		// Fetch into an PDOStatement object
		$request = $db->prepare("SELECT * FROM Settings");
		$request->execute();

		// Into array
		$settings = $request->fetchAll();

  		// Close the database connection
    		$db = null;

	} catch(PDOException $e) {
		print 'Exception : '.$e->getMessage();
	}
	return $settings;
}
?>
