<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/7
 * Time: 15:51
 */

namespace App\Common\http;


use Co\Http\Client;
use Swoft\Log\Helper\CLog;

class HttpUtils
{
      public static function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            array_push($valueArr,$key.'='.$val) ;
        }
        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        return $combined;
      }

      public static  function request($url, $params , $ispost = false, $https = false)
      {
          $urlArrays = parse_url($url);
          $cli = new Client($urlArrays['host'], $urlArrays['port'],$https ? true: null);
          //CLog::info(__METHOD__.' urlArrays:'.json_encode($urlArrays).' params:'.json_encode($params));
          if($ispost)
          {
              $cli->post($urlArrays['path'],$params);
          }
          else
          {
              //$getUrl = self::combineURL($urlArrays['path'],$params);
              $getUrl = $urlArrays['path'].'?'.http_build_query($params);
             // CLog::info(__METHOD__.' $getUrl:'.$getUrl);
              $cli->get($getUrl);
          }
          $statusCode = $cli->getStatusCode();
          $body = $cli->getBody();
          if($statusCode != 200 && $statusCode != 201)
          {
              CLog::error(__METHOD__.' .url:'.$url.' request error statusCode:'.$statusCode.
              ' body:'.$body);
              throw  new Client\Exception(' url:'.$url.' request error statusCode:'.$statusCode.
                  ' body:'.$body,5011);
          }
          return $body;
         // return [$statusCode,$body];
      }
}