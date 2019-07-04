<?php


namespace app\admin\queue;


use think\queue\Job;

/**
 * Class Job1
 * @package app\admin\queue
 */
class Job1 extends Job
{

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        // TODO: Implement getJobId() method.
    }

    /**
     * Get the number of times the job has been attempted.
     * @return int
     */
    public function attempts()
    {
        // TODO: Implement attempts() method.
    }

    /**
     * Get the raw body string for the job.
     * @return string
     */
    public function getRawBody()
    {
        // TODO: Implement getRawBody() method.
    }

    public function task1(Job $job,$data){
        sleep(2);
        echo time()."\r\n";
        echo 'task1'."\r\n";

        print_r($data);
        $job->delete();
    }
    public function task2(Job $job,$data){
        echo 'task2'."\r\n";
        print_r($data);
        $job->delete();
    }

    public function task3(Job $job,$data){
        echo 'task3'."\r\n";
        print_r($data);
    }
}