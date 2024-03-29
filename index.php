<?php

/**
 * ArtMoi PHP Portfolio Example
 */

require 'flight/flight/Flight.php';
require 'config.php';

// path for classes
Flight::path(HOME_PATH.'/classes');

// Register classes with Flight
Flight::register('artmoiController', 'Artmoi_Controller');
Flight::register('artmoiRequest', 'Artmoi_Request');
Flight::register('artmoiResponse','Artmoi_Response');

// 404 Error page
Flight::map('notFound',function(){
    Flight::view()->set('body_content',"Sorry! 404 Page not Found");
    Flight::render('header',array(),'header_content');
    Flight::render('template', array());
});

// Biography Page
Flight::route('/bio',function(){
    Flight::render('header',array(),'header_content');
    Flight::render('bio/body', array(), 'body_content');
    Flight::render('template', array("pageName" => "Biography"));
});

// Contact Page
Flight::route('/contact',function(){
    $js = array("/~andrewvalko/scripts/contact.js");
    Flight::render('header',array(),'header_content');
    Flight::render('contact/body', array(), 'body_content');
    Flight::render('template', array("js" => $js, "pageName" => "Contact"));
});

// Display collections & items
Flight::route('/@page(/@collectionNumber)',function($page,$collectionNumber){
    $js = array("/~andrewvalko/scripts/jquery.arrowNavigation.js");
    $content = Flight::get('content');

    $items = array();
    error_log("collection number is ".$collectionNumber);

    // if collection number exists, load creation page
    if($collectionNumber || $collectionNumber == "0")
    {
        $body = "creation/body";
        // Grab one item data from collection
        $p = (Flight::request()->query->p) ? Flight::request()->query->p : 0;
        Flight::view()->set("page",$p);
        $items = Flight::artmoiController()->collection($content[$page]['collections'][$collectionNumber]['id'], $p, 1);
    }
    else
    {
        Flight::render('header',array(),'header_content');
        $body = "collection/body";
        foreach ($content as $k => $v)
        {
            if($k == $page) // match with array key name and page name
            {
                $collections = $v; // add collection list to $collections
                foreach($collections as $collection)
                {
                    foreach($collection as $c)
                    {
                        // add to item array. $items[ collection name ] = collection data
                        $items[$c['name']] = Flight::artmoiController()->collection($c['id']);
                    }
                }
            }
        }
    }
    Flight::render($body, array("collections" => $items, "collectionNumber" => $collectionNumber,"pageName" => $page), 'body_content');
    Flight::render('template', array("pageName" => $page, "js" => $js));
});

// Home Page
Flight::route('/', function(){
    Flight::render('home/body', array(), 'body_content');
    Flight::render('template', array());
});
 

Flight::start();