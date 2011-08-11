<?php
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set("display_errors", 'on');

require_once 'Comment.php';

$act = isset($_POST['act'])?$_POST['act'] : '';
$item_id = isset($_POST['item_id'])?$_POST['item_id'] : '0';

if ($act == 'get_commets')
    sendJSONResponse(getComments($item_id));
elseif ($act == 'save_commet')
    sendJSONResponse(saveComment($item_id, $_POST['description'], $_POST['rate']));
elseif ($act == 'get_rates')
    sendJSONResponse(array(
    	'code'=>0
        , 'message'=>''
        , 'data' => Comment::getRates($_POST['item_ids'])
    ));
else
    sendJSONResponse(array('code'=>1, 'message'=>'Action is not correct'));


function saveComment($item_id, $desc, $rate)
{
    try {
        $comment = new Comment();
        $comment
            ->set_description($desc)
            ->set_item_id($item_id)
            ->set_guest_ip(getRemoteIP())
            ->set_guest_name(getGuestName(getRemoteIP()))
            ->set_rate($rate)
            ->save();
        return array('code'=>0, 'message'=>'', 'data' => Comment::BaseEntities2Array(array($comment)));
    }catch (Exception $ex)
    {
        return array('code'=>$ex->getCode(), 'message'=>$ex->getMessage(), 'data' => '');
    }
}


function getComments($item_id)
{
    $comments = Comment::getByItemId($item_id);
    $rating = 0;
    foreach ($comments as $comment)
    {
        $rating += $comment->get_rate();
    }
    $rating = count($comments)>0?round($rating/count($comments), 2):0;
    return array(
    	'code'=>0
        , 'message'=>''
        , 'data' => Comment::BaseEntities2Array($comments)
        , 'rate' => $rating
    );
}

/**
 * Get remote ip address if user behind nginx
 */
function getRemoteIP()
{
	if (array_key_exists('HTTP_X_REAL_IP', $_SERVER))
		return $_SERVER['HTTP_X_REAL_IP'];
	return $_SERVER['REMOTE_ADDR'];
}


function getGuestName($ip)
{
// get user name from TrafficPanel
//    $data = file_get_contents("http://192.168.1.201:8585/cgi-bin/get_ip_data.pl?ip=$ip");
//    if (!empty($data))
//    {
//    	$data = explode("|", $data);
//    	return $data[0];
//    }
    return "Anonymous [$ip]";
}

function sendJSONResponse($response) 
{
	header ( "Content-type: text/x-json; charset=UTF-8");
	echo json_encode ( $response );
	exit ();
}