<?php

namespace Route;

use Model\Model;
use Pagon\Route\Rest;
use Route\Web;

class SiteMap extends Rest
{
    protected $tpl = 'index.php';

    public function get()
    {
        $this->app->loadOrm();

        $posts = Model::factory('Article')->where('status', '1')->order_by_desc('point')->limit(100)->find_many();

        $xml = new \XMLWriter();
        $xml->openMemory();

        $xml->startDocument();
        $xml->startElement('urlset');
        foreach ($posts as $post) {
            $xml->startElement('url');

            $xml->startElement('loc');
            $xml->writeCdata($post->permalink());
            $xml->endElement();

            $xml->startElement('lastmod');
            $xml->writeCdata(date(DATE_ATOM, strtotime($post->modified_at ? $post->modified_at : $post->created_at)));
            $xml->endElement();

            $xml->startElement('changefreq');
            $xml->writeCdata('always');
            $xml->endElement();

            $xml->startElement('priority');
            $xml->writeCdata('1.0');
            $xml->endElement();

            $xml->endElement();
        }
        $xml->endElement();

        $this->output->contentType('xml');
        $this->output->write($xml->outputMemory());
    }
}