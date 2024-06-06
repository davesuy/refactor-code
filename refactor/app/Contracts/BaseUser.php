<?php

namespace DTApi\Contracts;
use DTApi\Helpers\TeHelper;
use DTApi\Models\Customer;
use DTApi\Models\Translator;
use DTApi\Models\User;
abstract class BaseUser implements UserInterface
{
    protected User $currentUser;

    public function sendNotification(Job $job, Job $translator)
    {
        if ($job->due->diffInHours(Carbon::now()) > 24) {
            $customer = $job->user()->get()->first();
            if ($customer) {
                $data = array();
                $data['notification_type'] = 'job_cancelled';
                $language = TeHelper::fetchLanguageFromJobId($job->from_language_id);
                $msg_text = array(
                    "en" => 'Er ' . $language . 'tolk, ' . $job->duration . 'min ' . $job->due . ', har avbokat tolkningen. Vi letar nu efter en ny tolk som kan ersätta denne. Tack.'
                );
                if ($this->isNeedToSendPush($customer->id)) {
                    $users_array = array($customer);
                    $this->sendPushNotificationToSpecificUsers($users_array, $job->id, $data, $msg_text, $this->isNeedToDelayPush($customer->id));     // send Session Cancel Push to customer
                }
            }
            $job->status = 'pending';
            $job->created_at = date('Y-m-d H:i:s');
            $job->will_expire_at = TeHelper::willExpireAt($job->due, date('Y-m-d H:i:s'));
            $job->save();
//                Event::fire(new JobWasCanceled($job));
            Job::deleteTranslatorJobRel($translator->id, $job->id);

            $data = $this->jobToData($job);

            $this->sendNotificationTranslator($job, $data, $translator->id);   // send Push all sutiable translators
            $response['status'] = 'success';
        } else {
            $response['status'] = 'fail';
            $response['message'] = 'Du kan inte avboka en bokning som sker inom 24 timmar genom DigitalTolk. Vänligen ring på +46 73 75 86 865 och gör din avbokning over telefon. Tack!';
        }
    }
}