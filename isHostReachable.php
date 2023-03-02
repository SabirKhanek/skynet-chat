<?php
if (!function_exists('isHostReachable')) {
    function isHostReachable($host)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $responseData = json_decode($response, true);
        if ($status == 200 && isset($responseData["status"]) && $responseData["status"] == "sky_success") {
            return true;
        }
        return false;
    }
}
