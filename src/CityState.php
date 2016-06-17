<?php namespace Vivek\CityState;

require_once 'vendor/autoload.php';

use ZipArchive;
use \Symfony\Component\Yaml\Yaml;

class CS {
    
    private static $MAXMIND_ZIPPED_URL = "http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip";
    private static $MAXMIND_DB_FILE = "../db/GeoLite2-City-Locations-en.csv";
    private static $COUNTRIES_FILE = "../db/countries.yml";
    
    //CSV positions
    private static $ID = 0;
    private static $COUNTRY = 4;
    private static $COUNTRY_LONG = 5;
    private static $STATE = 6;
    private static $STATE_LONG = 7;
    private static $CITY= 10;
    
    private static $countries = array();
    
    public static function updateMaxMind() {
        echo "Downloading file\n";
        //file_put_contents("../db/CityState.zip", fopen("http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip", 'r'));
    
        $zip = new ZipArchive;
        
        $enFile;
        
        
        echo "Looking for CSV file\n";
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
    
    
    public static function update() {
        self::updateMaxMind();
    }
    
    
    //List all countries in the world from countries.yml
    public static function countries() {
        if(!file_exists(self::$COUNTRIES_FILE)) {
            //Whoops. No countries.yml file. Get it from MAXMIND_DB_FILE
            echo "No countries.yml file. \n";
            if(!file_exists(self::$MAXMIND_DB_FILE)) {
                //Whoops. MAXMIND_DB_FILE does not exist. Download and extract from zip
                echo "No Maxmind CSV file. Redownloading";
                
                //TODO: Download and extract the CSV file;
                
            }
            
            $handle = fopen(self::$MAXMIND_DB_FILE, 'r');
                
            while (($line = fgets($handle)) !== false) {
                $rec = explode(",", $line);
                
                if(empty($rec[self::$COUNTRY]) || empty($rec[self::$COUNTRY_LONG]) || strlen($rec[self::$COUNTRY]) > 2) {
                    continue;
                }
               
                $country = strtoupper($rec[self::$COUNTRY]);
                            
                if(!array_key_exists($country, self::$countries)) {
                    self::$countries[$country] = str_replace("\"", "", $rec[self::$COUNTRY_LONG]);
                }
 
            }
            
            ksort(self::$countries);
            
            $yaml = Yaml::dump(self::$countries);
            file_put_contents(self::$COUNTRIES_FILE, $yaml);       
            echo "Done reading DB and generating countries\n";
            
            
           
        //End logic to generate countries.yml
        } else {
            //File exists. just read from YAML
            self::$countries = Yaml::parse(file_get_contents(self::$COUNTRIES_FILE));
        }
    }
}

//CS::updateMaxMind();

//CS::generateCountries();

CS::countries();