<?php /**/

namespace AppBundle\Service;

class LocationService
{
    const GOOGLE_API_KEY = 'AIzaSyAFenKZ1t8RZbQBswYPG2B_fs3c0rQSXMQ';

    public function getCoordinatesFromString(string $string)
    {
        $curl = curl_init();
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($string).'&key=' . self::GOOGLE_API_KEY;
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = json_decode(curl_exec($curl));
        if($result->status === 'OK') {
            if(count($result->results) > 0) {
                $result = $result->results[0];
                $lat = $result->geometry->location->lat;
                $lng = $result->geometry->location->lng;
                return [
                    'latitude' => $lat,
                    'longitude' => $lng
                ];
            }
        }

        return ['latitude' => null, 'longitude' => null];
    }

    public function distance($lat1, $long1, $lat2, $long2)
    {
        $R = 6371e3; // metres
        $angle1 = $this->degToRad($lat1);
        $angle2 = $this->degToRad($lat2);
        $diff1 = $this->degToRad($lat2-$lat1);
        $diff2 = $this->degToRad($long2-$long1);

        $a = sin($diff1/2) * sin($diff1/2) +
            cos($angle1) * cos($angle2) *
            sin($diff2/2) * sin($diff2/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $d = $R * $c / 1000;
    }

    /**
     * Converts degrees to radians
     *
     * @param float $degrees
     * @return float
     */
    private function degToRad(float $degrees) : float
    {
        return $degrees * 2 * pi() / 360;
    }
}