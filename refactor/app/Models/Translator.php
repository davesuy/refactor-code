<?php

namespace DTApi\Models;

class Translator extends BaseUser
{
    public function getJobs()
    {
        $cuser = $this->currentUser;
        $jobs = Job::getTranslatorJobs($cuser->id, 'new');
        return $jobs->pluck('jobs')->all();
    }

    public function getType()
    {
        return 'translator';
    }
}