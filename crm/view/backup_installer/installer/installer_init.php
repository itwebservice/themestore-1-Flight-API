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

//===============================Copying project to backup directory start=======================================//
$src = "../../";
function copy_directory($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
            	if($file!="installers" && $file!="backup_installer"){
            		copy_directory($src . '/' . $file,$dst . '/' . $file); 	
            	}                
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}
//===============================Copying project to backup directory end=======================================//


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
        
        // Add DROP TABLE if exists statement with backticks
        $return .= 'DROP TABLE IF EXISTS `' . $table . '`;';
        
        // Get the column names wrapped in backticks
        $cols = mysqlQuery('SHOW COLUMNS FROM ' . $table);
        while ($row_cols = mysqli_fetch_assoc($cols)) {
            array_push($fields_array, '`' . $row_cols['Field'] . '`'); // Wrap each field in backticks
        }
        $fields = implode(',', $fields_array);
        
        // Get the CREATE TABLE statement with backticks around table and column names
        $row2 = mysqli_fetch_row(mysqlQuery('SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . str_replace('`' . $table . '`', '`' . $table . '`', $row2[1]) . ";\n\n";
        
        // Loop through rows and create INSERT INTO statements with backticks
        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = mysqli_fetch_row($result)) {
                $return .= 'INSERT INTO `' . $table . '` (' . $fields . ') VALUES(';
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $return .= ");\n";
            }
        }
        $return .= "\n\n\n";
    }

    // Save the SQL backup file
    $sql_file = $db_dir . '/db-backup.txt';
    // Open the file and write the backup data
    $handle = fopen($sql_file, 'w+');
    fwrite($handle, $return);
    fclose($handle);
    
    // Adjust the file path for the URL
    $sql_file = explode('..', $sql_file)[1];
    $newUrl = preg_replace('/(\/+)/', '/', $sql_file);
    echo BASE_URL . 'view/backup_installer' . $newUrl;
}


backup_itours_db($db_dir);
//===============================Taking database backup end=======================================//
exit;
?>