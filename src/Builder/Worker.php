<?php

/**
 * @file
 * Working demo of simulating parallel process in PHP.
 */

// ****                       **** //
print "Main program started.... \n";
// Start two child processes.
createProcess("Job1");
createProcess("Job2");
print " *** Child Process started *** \n";

while (TRUE) {

    $pid = pcntl_waitpid(0, $status, WNOHANG);
    if ($pid > 0) {
        childProcessComplete($pid);
    }else if($pid === -1){
        print " *** Child Process Completed *** \n";
        allChildProcessComplete();
        exit();
    }

}
print "Main program end. \n";

/**
 * Method to start child process.
 */
function startChildProcess($childProcessName) {
    $executionTime = rand(5, 10);

    if($childProcessName === "Job1"){
        print "Starting Job1 on processId " .getmypid()."  at " . date('l jS \of F Y h:i:s A') . " expected time $executionTime seconds\n";
    }else if($childProcessName === "Job2"){
        print "Starting Job2 on processId " .getmypid()."  at " . date('l jS \of F Y h:i:s A') . " expected time $executionTime seconds\n";
    }

    // Simulate doing actual work with sleep().
    sleep($executionTime);
}

/**
 * Method to notify when system unable to start a process.
 */
function errorOnProcessLunch() {
    print "Failed to lunch process \n";
}

/**
 * Method to notify when a child process complete the task.
 */
function childProcessComplete($pid) {
    print "Child processing is done for $pid  at " . date('l jS \of F Y h:i:s A') . " \n";
}

/**
 * Method to create a new process.
 */
function createProcess($pname) {
    $pid = pcntl_fork();

    if ($pid == -1) {
        errorOnProcessLunch();
    }
    else if ($pid === 0) {
        startChildProcess($pname);
        exit(); // Make sure to exit.
    }
    else {
        startParentProcess($pid);
    }

}

/**
 * Method to notify when parent thread execute.
 */
function startParentProcess($childProcessID){
    print "In parent thread created child processid $childProcessID \n";
}

/**
 * Method to notify when all child process has completed the task.
 */
function allChildProcessComplete(){
    print "All child processing completed \n";
}

