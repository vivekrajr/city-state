<?php namespace Vivek\CityState;

use ZipArchive;

class CS {
    
    private static $MAXMIND_ZIPPED_URL = "http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip";
    private static $MAXMIND_DB_FILE = "../db/GeoLite2-City-Locations-en.csv";
    
    public static function updateMaxMind() {
        //file_put_contents("../db/CityState.zip", fopen("http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip", 'r'));
    
        $zip = new ZipArchive;
        
        $enFile;
        
        if ($zip->open("../db/CityState.zip") === TRUE) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex( $i );      
                if(strpos($stat['name'], 'GeoLite2-City-Locations-en') !== false) {
                    $enFile = $stat['name'];
                }               
            }
        }
        echo $enFile, "\n";
        echo self::$MAXMIND_ZIPPED_URL, "\n";
        $fileName = explode('/', $enFile)[1];
        
        copy("../db/".$enFile, "../db/".$fileName); 
    }
    
    public static function generateCountries() {
        if(($handle = fopen(self::$MAXMIND_DB_FILE, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                    echo "<p> $num fields in line $row: <br /></p>\n";
                    $row++;
                for ($c=0; $c < $num; $c++) {
                    echo $data[$c] . "<br />\n";
                }
            }    
        }
    }
}

CS::generateCountries();