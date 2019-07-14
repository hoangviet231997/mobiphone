<?php
namespace App\Helpers;

class Helper{

     /**
     * In Time Range
     *
     * @param $time_start, $time_now, $time_end
     * Date time with format: "yyyy-mm-dd H:i:s"
     * @return bool (true or false)
     */
    public static function inTimeRange($time_start, $time_now, $time_end) {
        $t1 = strtotime("{$time_start}");
        $t2 = strtotime("{$time_end}");
        $tn = strtotime("{$time_now}");
        if ($t1 >= $t2) $t2 = strtotime('+1 day', $t2);
        return ($tn >= $t1) && ($tn <= $t2);
    }

    /**
     * Calculate Age
     *
     * @param $birthday
     * Date time with format: "yyyy-mm-dd"
     * @return integer Age
     */
    public  static function calculateAge($birthday){
        $today=date_create(date("Y-m-d"));
        $birthday=date_create($birthday);
        $age=date_diff($birthday,$today);
        return $age->y;
    }

    public static function spCurl($url, $data, $method){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return (array)json_decode($response);
    }

    public function intersects($lhs, $rhs) {
        return !($lhs['start'] > $rhs['end'] || $lhs['end'] < $rhs['start']);
    }

    public function checkDateOverlap() {
        $ranges = [
            ["start"=>"2018-11-01 11:01:00","end"=>"2018-11-01 12:00:00"],
            ["start"=>"2018-11-01 10:00:00","end"=>"2018-11-01 11:00:00"],
            ["start"=>"2018-11-01 12:01:00","end"=>"2018-11-01 13:00:00"],
            ["start"=>"2018-11-01 09:00:00","end"=>"2018-11-01 10:00:00"]
        ];
        for($i = 0; $i < sizeof($ranges); $i++) {
            for($j = $i+1; $j < sizeof($ranges); $j++) {
                if($this->intersects($ranges[$i], $ranges[$j])) {
                    echo "Date {$i} intersects with date {$j}\n";
                    return 0;
                }
            }
        }
        return 1;
    }

    public static function request($method = "GET", $endpoint = '/', $option = null){
        $result = [
            "status_code" => 200,
            "data" => null,
            "message" => 'Success'
        ];

        try {
            $client = new Client(['base_uri' => env('API_MAIN')]);

            $response = $client->request($method, $endpoint, $option);

            $result["data"] = json_decode($response->getBody()->getContents(), true);

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $result["status_code"] = $response->getStatusCode();

            if(401 == $result["status_code"]) {
                $result["message"] = 'Invalid credentials';
            }
            elseif(403 == $result["status_code"]){
                $result["message"] = 'Access is denied';
            }
            else {
                $message = json_decode($response->getBody()->getContents(), true);
                $message = isset($message["message"]) ? $message["message"] : $message;

                $result["message"] = 'An error has occurred. Message: ' . $message;
            }
        }

        return $result;
    }

    public static function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

