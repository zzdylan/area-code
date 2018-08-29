<?php

namespace ZzDylan\AreaCode;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class Code
{
    protected $baseUrl = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/';
    protected $dom;
    protected $client;
    public function __construct()
    {
        $this->dom = new Dom;
        $this->client = new Client();
    }

    public function get(){
        $client = $this->client;
        $response = $client->get($this->getNewUrl());
        //$provinceHtml = $response->getBody();
        $provinceHtml = iconv('GB2312', 'UTF-8', $response->getBody());
        //echo $provinceHtml;exit();
        $dom = $this->dom;
        $dom->load($provinceHtml);
        $html = $dom->outerHtml;
        echo $html;
    }


    public function getNewUrl(){
        $dom = $this->dom;
        $dom->load($this->baseUrl);
        $centerListContlist = $dom->find('.center_list_contlist')[0];
        $dom->load($centerListContlist);
        $li = $dom->find('li')[0];
        $dom->load($li);
        $a = $dom->find('a')[0];
        $href = (string)$a->getAttribute('href');
        return $href;
    }
}