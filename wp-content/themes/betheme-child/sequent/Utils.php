<?php
/**
 * Created by PhpStorm.
 * User: luisrosales
 * Date: 09/12/2018
 * Time: 16:19
 */

class Utils
{
    public static function addProspect($data, $baseUrl, $apiKey, $sequentKey)
    {



        $jsonData = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl."/addprospect",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "x-api-key: ". $apiKey,
                "x-sequent-key: ".$sequentKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return [ 'message'=>  "cURL Error #:" . $err, 'code'=> 400 ];
        } else {
            return $response;
        }
    }
}