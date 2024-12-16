<?php
include_once('../../../model/model.php');

//===============================Creating Backup directory start=======================================//
$timestamp = date('d-m-y').'('.date('U').')';

function check_dir($current_dir, $type)
{	 	
	if(!is_dir($current_dir."/".$type))
	{
		mkdir($current_dir."/".$type);		
	}	
	$current_dir = $current_dir."/".$type;
	return $current_dir;	
}

$current_dir = '../backups';
$current_dir = check_dir($current_dir , $timestamp);
$db_dir = check_dir($current_dir , 'db');
//===============================Creating Backup directory end=======================================//

//===============================Taking database backup start=======================================//
function backup_itours_db($db_dir, $tables = '*')
{
    // Get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = mysqlQuery('SHOW TABLES');
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }
    $return = '';
    
    // Cycle through each table
    foreach ($tables as $table) {
        $fields_array = array();
        $result = mysqlQuery('SELECT * FROM ' . $table);
        $num_fields = mysqli_num_fields($result);
        
        // Add DROP TABLE if exists statement
        $return .= 'DROP TABLE IF EXISTS `' . $table . '`;';
        
        // Get the CREATE TABLE statement
        $row2 = mysqli_fetch_row(mysqlQuery('SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";
        
        // Insert data into table
        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = mysqli_fetch_row($result)) {
                $return .= 'INSERT INTO `' . $table . '` VALUES(';
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $return .= isset($row[$j]) ? '"' . $row[$j] . '"' : '""';
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $return .= ");\n";
            }
        }
        $return .= "\n\n\n";
    }

    // Save the SQL backup file as .txt
    $sql_file = $db_dir . '/db-backup.txt';
    file_put_contents($sql_file, $return);

    // Trigger download with correct headers
    if (file_exists($sql_file)) {
        // Ensure no output is sent before headers
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Set headers for text file download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="db-backup.txt"');
        header('Content-Length: ' . filesize($sql_file));
        readfile($sql_file);
        exit;
    } else {
        echo "Backup file could not be created.";
    }
}

backup_itours_db($db_dir);
//===============================Taking database backup end=======================================//
exit;
