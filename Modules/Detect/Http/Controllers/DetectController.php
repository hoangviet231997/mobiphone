<?php

namespace Modules\Detect\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\BaseController;
use Modules\Message\Entities\Message;
use Modules\Rule\Entities\Rule;
use Modules\Person\Entities\Person;
use Modules\Person\Entities\PersonImage;

class DetectController extends BaseController
{
    public function getMessages($person){
        $rules = Rule::where('rule_status', Rule::$active)->get();
        $correct_rules = [];
        foreach ($rules as $key => $rule){
            $check_in_time_range = Helper::inTimeRange($rule->rule_start_time, date('H:i:s') , $rule->rule_end_time);
            if($check_in_time_range){
                $correct_rules[] = $rule->toArray();
            }
        }
        if(count($correct_rules) == 0) return;
        $message = $this->replaceVariable($correct_rules[array_rand($correct_rules)], $person);
        return $message;
    }

    public function replaceVariable($message,$person){
        switch (getenv('MF_LANGUAGE')){
            case 'vi': $mr = "Anh"; $ms = "Chị";break;
            case 'en': $mr = "Mr"; $ms = "Ms";break;
            default: $mr = "Mr"; $ms = "Ms";
        }
        foreach ( $message as $key => $value){
            $message[$key] = str_replace('[last_name]',$person->person_last_name,$message[$key]);
            $message[$key] = str_replace('[first_name]',$person->person_first_name,$message[$key]);
            $message[$key] = str_replace('[gender]',$person->person_gender==1?$mr:$ms,$message[$key]);
        }
        return $message;
    }

    public function sendMessages($person , $data){
        $message = $this->getMessages($person);
        if($message == null) return "There are not any rules that satisfy the conditions.";
        $person->time_last_join == NULL? $check_time_send_message = getenv('MF_TIME_SEND_MESSAGE'):$check_time_send_message = strtotime(date('Y:m:d H:i:s')) - strtotime($person->time_last_join);
        if($check_time_send_message >= getenv('MF_TIME_SEND_MESSAGE')) {
            $image_source = $data['image_source'];
            $image_detect = $data['image_detect'];
            $score = $data['score'];
            $data_send_1 = [
                'message' => [
                    'description' => $message['rule_content'],
                    'text_to_speech' => strip_tags($message['rule_content']),
                    'file' => $image_source,
                ],
                'hit_timestamp' => (string)strtotime(date('Y-m-d H:i:s')),
                'type' => $message['rule_type']
            ];
            if ($message['rule_title'] != NULL) {
                $data_send_1['message']['title'] = $message['rule_title'];
            }
            $data_send_2 = [
                "user_info" => [
                    "avatar_detect" => $image_detect,
                    "score_detect" => round($score),
                    "hit_timestamp" => (string)strtotime(date('Y-m-d H:i:s')),
                ]
            ];
            Helper::spCurl(getenv("NODE_SOCKET_SEVER") . '/user-info', (object)$data_send_2, "POST");
            Helper::spCurl(getenv("NODE_SOCKET_SEVER"), (object)$data_send_1, "POST");
            Message::create(['person_id' => $person->person_id, 'screen_id' => 1, 'message_type' => $message['rule_type'], 'message_content' => json_encode($data_send_1)]);
            Message::create(['person_id' => $person->person_id, 'screen_id' => 1, 'message_type' => $message['rule_type'], 'message_content' => json_encode($data_send_2)]);
            $person->update(['time_last_join' => date('Y:m:d H:i:s')]);
            return $data_send_1;
        }
        $come_back_time = getenv('MF_TIME_SEND_MESSAGE') - $check_time_send_message;
        return "Please come back after ".$come_back_time." seconds to be recognized by the system.";
    }
}
