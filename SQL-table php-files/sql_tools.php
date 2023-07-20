<?php

$YOUR_DATABASE_NAME = 'your_database_name'; // the name of the database you're accessing

function readSQL($sqlStr, $convertRowsToArrays=TRUE) {
    // create SQL connection
    $conn = mysqli_connect("localhost", "sql_user_name - must have at least read access", "sql_password", $YOUR_DATABASE_NAME); // don't forget to add your own SQL sign-in info
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
function writeSQL($sqlStr) {
    // create SQL connection
    $conn = mysqli_connect("localhost", "sql_user_name - must have writing access", "sql_password", $YOUR_DATABASE_NAME); // don't forget to add your own SQL sign-in info
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // send the query and return result
    $out = mysqli_query($conn, $sqlStr);
    // close connection
    mysqli_close($conn);
	return $out;
}

function updateTableRowFromArray($tableName, $rowSelector, $arr, $debug=false) {
	// get a list of the names of each column
	$tableHeaders = readSQL("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$YOUR_DATABASE_NAME."' AND TABLE_NAME = '".$tableName."'");
	
	// create a sql writing string
	$dontUpdateTheseCols = ['Referral Type', 'id', 'Date and Time']; // list of column names that you do not want to allow access to edit (for security reasons)
	$writeThis = array();
	for ($i = 0; $i < count($tableHeaders); $i++) {
    	if (in_array($tableHeaders[$i][0], $dontUpdateTheseCols)) {
        	continue;
        }
 		array_push($writeThis, "`".addslashes($tableHeaders[$i][0])."`=".addQuotes($arr[$i]));
	}

	// run it
	$updateStr = 'UPDATE `'.$tableName.'` SET '.join(", ",$writeThis).' WHERE '.$rowSelector;
	if ($debug) {
    	echo($updateStr);
    	echo('<br>');
    }
	return writeSQL($updateStr);
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