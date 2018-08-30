<?php

namespace ZzDylan\AreaCode;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class Code
{
    protected $baseUrl = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/';
    protected $newUrl;
    protected $dom;
    protected $client;
    protected $data= [];
    public function __construct()
    {
        $this->dom = new Dom;
        $this->client = new Client();
    }

    public function save($path=''){
        if(!$path){
            $path = './area.json';
        }
        $data = $this->getData();
        file_put_contents($path, json_encode($data));
    }


    public function getData(){
        $this->newUrl = $this->getNewUrl();
        $response = $this->client->get($this->newUrl);
        $provinceHtml = iconv('GB2312', 'UTF-8', $response->getBody());
        $provinceHtml = str_replace('gb2312','utf-8',$provinceHtml);
        $dom = $this->dom;
        $dom->load($provinceHtml);
        $provinceAArr = $dom->find('a');
        $data = [];
        foreach($provinceAArr as $key=>$provinceA){
            $data[$key]['name'] = $provinceA->text;
            $data[$key]['code'] = '';
            $cityUrl = str_replace('index.html', $provinceA->getAttribute('href'), $this->newUrl);
            $response = $this->client->get($cityUrl);
            $cityHtml = iconv('GB2312', 'UTF-8', $response->getBody());
            $cityHtml = str_replace('gb2312','utf-8',$cityHtml);
            $this->dom->load($cityHtml);
            $citytrArr = $this->dom->find('.citytr');
            foreach($citytrArr as $cityKey=>$cityTr){
                $this->dom->load($cityTr);
                $cityAArr = $this->dom->find('a');
                $cityCode = $cityAArr[0]->text;
                $cityName = $cityAArr[1]->text;
                $data[$key]['city'][$cityKey]['name'] = $cityName;
                $data[$key]['city'][$cityKey]['code'] = substr($cityCode,0,6);
                $provinceCode = str_pad(substr($cityCode,0,2),6,0,STR_PAD_RIGHT);
                $data[$key]['code'] = $provinceCode;
                $countyUrl = str_replace('index.html', $cityAArr[0]->getAttribute('href'), $this->newUrl);
                $response = $this->client->get($countyUrl);
                // echo $cityUrl."\n\r";
                // echo $countyUrl."\n\r";
                $countyHtml = iconv('gbk', 'UTF-8', $response->getBody());
                $countyHtml = str_replace('gb2312','utf-8',$countyHtml);
                $this->dom->load($countyHtml);
                $countytrArr = $this->dom->find('.countytr');
                if(!count($countytrArr)){
                    $countytrArr = $this->dom->find('.towntr');
                }
                foreach($countytrArr as $countyKey=>$countyTr){
                    $this->dom->load($countyTr);
                    $countyAArr = $this->dom->find('a');
                    if(!count($countyAArr)){
                        $countyAArr = $this->dom->find('td');
                    }
                    $countyCode = $countyAArr[0]->text;
                    $countyName = $countyAArr[1]->text;
                    echo $provinceA->text.' '.$cityName.' '.$countyName."\n\r";
                    $data[$key]['city'][$cityKey]['county'][$countyKey]['name'] = $countyName;
                    $data[$key]['city'][$cityKey]['county'][$countyKey]['code'] = substr($countyCode,0,6);
                }
            }
            sleep(1);
        }
        $this->data = $data;
        return $this->data;
    }


    public function getNewUrl(){
        $dom = $this->dom;
        $dom->load($this->baseUrl);
        $centerListContlist = $dom->find('.center_list_contlist')[0];
        $dom->load($centerListContlist);
        $li = $dom->find('li')[0];
        $dom->load($li);
        $a = $dom->find('a')[0];
        $href = $a->getAttribute('href');
        return $href;
    }

}