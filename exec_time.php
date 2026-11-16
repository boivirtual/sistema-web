<?php
    
   /*
    *
    * @file: exec_time.php
    *
    * @author: Angelito M. Goulart
    *
    * @date: 22/11/2011
    *
    * @description: calculates the time of execution of a script
    *
    * @use: include this file in the top of the script, call function startExec() and
    * the end of script call the function endExec().
    *
    * endExec function returns the result of running in seconds.
	* 
	* Requires PHP >= 5 
    *
    */
    
   global $time;
    
   /* Get current time */
   function getTime(){
      return microtime(TRUE);
   }
    
   /* Calculate start time */
   function startExec(){
      global $time;
      $time = getTime();
   }
    
   /*
    * Calculate end time of the script,
    * execution time and returns results
    */
   function endExec(){
      global $time;      
      $finalTime = getTime();
      $execTime = $finalTime - $time;
      return number_format($execTime, 6) . ' s';
   }
    
?>