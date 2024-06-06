<?php

namespace DTApi\Contracts;

interface UserInterface
{
    public function getJobs();

    public function getJobHistory();

    public function sendNotification(Job $job, Job $translator);
}