<?php

namespace DTApi\Models;

use DTApi\Contracts\BaseUser;
use DTApi\Helpers\TeHelper;

class Customer extends BaseUser
{
    public function getJobs()
    {
        $cuser = $this->currentUser;
        return $cuser->jobs()->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback')->whereIn('status', ['pending', 'assigned', 'started'])->orderBy('due', 'asc')->get();
    }

    public function getType()
    {
        return 'customer';
    }

    public function getJobHistory(array $emergencyJobs = [], $pageum = 1)
    {
        $jobs = $this->getJobs();
        $usertype = $this->getType();

        return ['emergencyJobs' => $emergencyJobs, 'noramlJobs' => [], 'jobs' => $jobs, 'cuser' => $this->currentUser, 'usertype' => $usertype, 'numpages' => 0, 'pagenum' => $pageum];
    }

    public function sendNotification(\DTApi\Contracts\Job $job, Job|\DTApi\Contracts\Job $translator)
    {
        $job->withdraw_at = Carbon::now();
        if ($job->withdraw_at->diffInHours($job->due) >= 24) {
            $job->status = 'withdrawbefore24';
            $response['jobstatus'] = 'success';
        } else {
            $job->status = 'withdrawafter24';
            $response['jobstatus'] = 'success';
        }
        $job->save();
        Event::fire(new JobWasCanceled($job));
        $response['status'] = 'success';
        $response['jobstatus'] = 'success';
        if ($translator) {
            $data = array();
            $data['notification_type'] = 'job_cancelled';
            $language = TeHelper::fetchLanguageFromJobId($job->from_language_id);
            $msg_text = array(
                "en" => 'Kunden har avbokat bokningen för ' . $language . 'tolk, ' . $job->duration . 'min, ' . $job->due . '. Var god och kolla dina tidigare bokningar för detaljer.'
            );
            if ($this->isNeedToSendPush($translator->id)) {
                $users_array = array($translator);
                $this->sendPushNotificationToSpecificUsers($users_array, $job->id, $data, $msg_text, $this->isNeedToDelayPush($translator->id));// send Session Cancel Push to Translaotor
            }
        }
    }
}