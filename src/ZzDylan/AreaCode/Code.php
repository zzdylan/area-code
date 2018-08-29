<?php

namespace ZzDylan\AreaCode;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class Code
{
    protected $baseUrl = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/';
    protected $dom;
    public function __construct()
    {
        $dom = new Dom;
        $this->dom = $dom;
    }

    public function get(){
        $client = new Client();
        $response = $client->get($this->getNewUrl());
        echo $response->getBody();exit();
        $dom = $this->dom;
        $dom->load($this->getNewUrl());
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