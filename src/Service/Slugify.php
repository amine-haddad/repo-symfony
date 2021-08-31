<?php
namespace App\Service;

class Slugify
{
    public function generate(string $string)
    {
        $slug = preg_replace('~[^\pL\d]+~u', '-',$string);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIST', $slug);
        $slug = preg_replace('~[^-\w]+~', '',$slug);
        $slug = trim($slug,'-');
        $slug = preg_replace('~-+~', '-',$slug);
        $slug = strtolower($slug);
        return $slug;
    }
}