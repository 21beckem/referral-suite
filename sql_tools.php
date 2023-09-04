<?php

require_once('sql_passwords.php');

function readSQL($YOUR_DATABASE_NAME, $sqlStr, $convertRowsToArrays=TRUE) {
    global $SQL_READER_PASS;
    // create SQL connection
    $conn = mysqli_connect("localhost", $SQL_READER_PASS[0], $SQL_READER_PASS[1], $YOUR_DATABASE_NAME); // don't forget to add your own SQL sign-in info
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // run sql
    $result = mysqli_query($conn, $sqlStr);
    // loop to store the data in an associative array.
    $out = array();
    $index = 0;
    while ($row = mysqli_fetch_assoc($result)) {
    	if ($convertRowsToArrays) {
        	$out[$index] = array_values($row);
        } else {
        	$out[$index] = $row;
        }
        $index++;
    }
    //close and return
    mysqli_close($conn);
    return $out;
}
function writeSQL($YOUR_DATABASE_NAME, $sqlStr) {
    global $SQL_WRITER_PASS;
    // create SQL connection
    $conn = mysqli_connect("localhost", $SQL_WRITER_PASS[0], $SQL_WRITER_PASS[1], $YOUR_DATABASE_NAME); // don't forget to add your own SQL sign-in info
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // send the query and return result
    $out = mysqli_query($conn, $sqlStr);
    // close connection
    mysqli_close($conn);
	return $out;
}

function readTableColumns($YOUR_DATABASE_NAME, $tableName) {
    $raw = readSQL($YOUR_DATABASE_NAME, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$YOUR_DATABASE_NAME."' AND TABLE_NAME = '".$tableName."'");
    for ($i=0; $i < count($raw); $i++) { 
        $raw[$i] = $raw[$i][0];
    }
    return $raw;
}
function readTableColumnInfo($YOUR_DATABASE_NAME, $tableName) {
    $q = <<<HERA
    select COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, 
        NUMERIC_PRECISION, DATETIME_PRECISION, 
        IS_NULLABLE 
    from INFORMATION_SCHEMA.COLUMNS
    where TABLE_NAME='{$tableName}'
    HERA;
    return readSQL($YOUR_DATABASE_NAME, $q);
}
function updateTableRowFromArray($YOUR_DATABASE_NAME, $tableName, $rowSelector, $arr, $addIfRowSelectorDoesntExist=false, $debug=false) {
	// get a list of the names of each column
	$tableHeaders = readTableColumns($YOUR_DATABASE_NAME, $tableName);

    // check if this rowSelector exists
    $makeNewRow = ( count(readSQL($YOUR_DATABASE_NAME, 'SELECT * FROM `'.$tableName.'` WHERE '.$rowSelector)) == 0 && $addIfRowSelectorDoesntExist );
	
	// create a sql writing string
	$dontUpdateTheseCols = ['Referral Type', 'id', 'Date and Time']; // list of column names that you do not want to allow access to edit (for security reasons)
	$writeThis = array();
    $writeThis2 = array();
	for ($i = 0; $i < count($tableHeaders); $i++) {
        if (in_array($tableHeaders[$i], $dontUpdateTheseCols) || $arr[$i]==NULL) {
            continue;
        }
        if ($makeNewRow) {
            array_push($writeThis, "`".addslashes($tableHeaders[$i])."`");
            array_push($writeThis2, addQuotes($arr[$i]));
        } else {
            array_push($writeThis, "`".addslashes($tableHeaders[$i])."`=".addQuotes($arr[$i]));
        }
	}
    
    if ($makeNewRow) {
        $updateStr = 'INSERT INTO `'.$tableName.'` ('.join(", ",$writeThis).') VALUES ('.join(", ",$writeThis2).')';
    } else {
        $updateStr = 'UPDATE `'.$tableName.'` SET '.join(", ",$writeThis).' WHERE '.$rowSelector;
    }
	// run it
	if ($debug) {
    	echo($updateStr);
    	echo('<br>');
    }
	return writeSQL($YOUR_DATABASE_NAME, $updateStr);
}
function updateTableRowFromObject($YOUR_DATABASE_NAME, $tableName, $rowSelector, $obj, $addIfRowSelectorDoesntExist=false, $debug=false) {
	// get a list of the names of each column
	$tableHeaders = readTableColumns($YOUR_DATABASE_NAME, $tableName);

    // check if this rowSelector exists
    $makeNewRow = ( count(readSQL($YOUR_DATABASE_NAME, 'SELECT * FROM `'.$tableName.'` WHERE '.$rowSelector)) == 0 && $addIfRowSelectorDoesntExist );
	
	// create a sql writing string
	$dontUpdateTheseCols = ['Referral Type', 'id', 'Date and Time']; // list of column names that you do not want to allow access to edit (for security reasons)
	$writeThis = array();
    $writeThis2 = array();
	for ($i = 0; $i < count($tableHeaders); $i++) {
        if (in_array($tableHeaders[$i], $dontUpdateTheseCols) || !isset($obj[ $tableHeaders[$i] ])) {
            continue;
        }
        if ($makeNewRow) {
            array_push($writeThis, "`".addslashes($tableHeaders[$i])."`");
            array_push($writeThis2, addQuotes($obj[ $tableHeaders[$i] ]));
        } else {
            array_push($writeThis, "`".addslashes($tableHeaders[$i])."`=".addQuotes($obj[ $tableHeaders[$i] ]));
        }
	}
    
    if ($makeNewRow) {
        $updateStr = 'INSERT INTO `'.$tableName.'` ('.join(", ",$writeThis).') VALUES ('.join(", ",$writeThis2).')';
    } else {
        $updateStr = 'UPDATE `'.$tableName.'` SET '.join(", ",$writeThis).' WHERE '.$rowSelector;
    }
	// run it
	if ($debug) {
    	echo($updateStr);
    	echo('<br>');
    }
	return writeSQL($YOUR_DATABASE_NAME, $updateStr);
}
function addQuotes($str) {
	if (gettype($str) == 'string') {
    	return '"'.addslashes($str).'"';
    }
	if ($str == NULL) {
    	return 'NULL';
    }
	return $str;
}

?>