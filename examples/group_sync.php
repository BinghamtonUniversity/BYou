<?php
chdir(dirname(__file__));

include_once('../app/Libraries/HTTPHelper.php');
use App\Libraries\HTTPHelper;

class IAMBingGroupSync
{
    static private $identities = [
        [
            'ids'=>['bnumber'=>'B00450942'],
            'first_name'=>'Ali Kemal',
            'last_name'=>'Tanriverdi',
        ],
        [
            'ids'=>['bnumber'=>'B00505893'],
            'first_name'=>'Tim',
            'last_name'=>'Cortesi',
        ],
        [
            'ids'=>['bnumber'=>'B00612268'],
            'first_name'=>'Lauri',
            'last_name'=>'Arnold',
        ],
        [
            'ids'=>['bnumber'=>'B00123467'],
            'first_name'=>'John',
            'last_name'=>'Doe',
        ],
        [
            'ids'=>['bnumber'=>'B00123234'],
            'first_name'=>'Tony',
            'last_name'=>'Stark',
        ],
        [
            'ids'=>['bnumber'=>'B00123458','otherid'=>1],
            'first_name'=>'New',
            'last_name'=>'Guy',
        ],
    ];

    static private $groups = [
        'staff'=>["B00450942"],
        // 'Avengers'=>['B0034346','B00123234','B00505893'],
        // 'Applicants'=>['B00612268','B00123467'],
    ];

    static private function get_identities($bnumbers) {
        $identities = [];
        foreach(self::$identities as $identity) {
            if (in_array($identity['ids']['bnumber'],$bnumbers)) {
                $identities[] = $identity;
            }
        }
        return $identities;
    }

    static private $iambing_url = 'http://iambing.local:8000';
    static private $iambing_username = 'defaultuser';
    static private $iambing_password = 'defaultpass';

    static public function sync() {
        $httphelper = new HTTPHelper();

        foreach(self::$groups as $group_name => $bnumbers) {
            echo "\n\nSYNCING GROUP: ".$group_name."\n";
            $group_name = str_replace(' ','%20',$group_name);
            $graphene_response = $httphelper->http_fetch([
                'url'  => self::$iambing_url.'/api/public/groups/'.$group_name.'/members',
                'verb' => 'POST',
                'data' => ['identities'=>self::get_identities($bnumbers),'id'=>'bnumber'],
                'username' => self::$iambing_username,
                'password' => self::$iambing_password,
                'headers' => ['Accept'=>'application/json'],
            ]);
            var_dump($graphene_response['content']);    
        }
    }

}

IAMBingGroupSync::sync();