<?
	
// this file contains various functions and calls that are called / used to perform various actions 

// https://www.geeksforgeeks.org/how-to-generate-a-random-unique-alphanumeric-string-in-php/  
// This function will return 
// A random string of specified length 
function random_strings($length_of_string) { 
    return substr(bin2hex(random_bytes($length_of_string)),  
                                      0, $length_of_string); 
} 

