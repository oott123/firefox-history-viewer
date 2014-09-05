<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();
$app->config('templates.path','./templates');
$app->config('debug',true);

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */
$databases = glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'*.sqlite');
// GET route
$app->get(
    '/',
    function () use($app,$databases){
        $rooturl = $app->request()->getRootUri();
        $app->render('index.php',array('rooturl'=>$rooturl,'dbcount'=>count($databases)));
    }
);

// POST route
$app->post(
    '/search',
    function () use($app,$databases){
        $in_ajax = $app->request->post('in_ajax');
        $keyword = $app->request->post('keyword');
        $page = $app->request->post('page');
        $page = $page?$page:1;
        $page_size = 100;
        $per_db = floor($page_size/count($databases));
        $page_count = 0;
        $limit = ' LIMIT '.$per_db.' OFFSET '.($page-1)*($per_db);
        $order = ' ORDER BY frecency DESC';
        $result = array();
        foreach($databases as $file){
            $like = 'LIKE \'%'.$keyword.'%\'';
            $count = "SELECT COUNT(*) FROM moz_places WHERE url {$like} or title {$like}";
            $sql = 'SELECT title,url FROM moz_places WHERE url '.$like.' or title '.$like.$order.$limit;
            $dbh = new PDO('sqlite:'.$file);
            $count = $dbh->query($count)->fetch(PDO::FETCH_ASSOC);
            $page_count+= $count['COUNT(*)'];
            $row = $dbh->query($sql);
            while($r = $row->fetch(PDO::FETCH_ASSOC)){
                $result[] = $r;
            }
        }
        $pages = floor($page_count/$page_size);
        $data = array(
            'keyword'=>$keyword,
            'result'=>$result,
            'pages'=>$pages,
            'page'=>$page,
            'count'=>$page_count,
        );
        if($in_ajax){
            echo json_encode($data);
        }else{
            $rooturl = $app->request()->getRootUri();
            $app->render('index.php',array(
                'rooturl'=>$rooturl,
                'dbcount'=>count($databases),
                'data'=>$data,
            ));
        }
    }
);


/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
