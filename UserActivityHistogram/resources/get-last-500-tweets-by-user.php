<?php
/**
 * @author Wei Zhang
 */
require "../../../../../lib/twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

const CONSUMER_KEY = '4dT1aRogxH2EicI9duvgswL9V';
const CONSUMER_SECRET = 'RfNaUwUmYjlDWyyYAsCLKZO7QDafRGToyO9hbKyvM3xL4gkb1a';
$access_token = '37612059-fdAJamGw53zqfLIKKOH98MQnjv0P8gGfy6LPKv2Nj';
$access_token_secret = 'IhYI4opixzZghx6oBbeGts1si9sfcor7nTSirFiuNYoBk';

// total number of tweets to request from twitter API
$totalTweetsRequired = 500;
// limit of tweets returned from user time line API.
$countPerRequest = 200;
// support sydney for now.
$currentTimezone = 'Australia/Sydney';

$username = $_REQUEST['twitterUsername'];
if(empty($username)){
	// return json with error message.
	$errorMsgArray = array(
		'success' => false,
		'message' => 'Twitter user name is empty.'
	);
	returnJsonResponse($errorMsgArray);
}

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);

// setup timezone and timezone object.
date_default_timezone_set($currentTimezone);
$currentTimezoneObj = new DateTimeZone($currentTimezone);

$maxId = null;
$stats = array();
/**
 * Send request to Twitter REST API until:
 * 1. the number of retrieved tweets is identical to the total number as required. 500 for now
 * 2. or this user has run out of tweets before reaching 500 tweets.
 */
for($tweetsRequested = 0; $tweetsRequested < $totalTweetsRequired; $tweetsRequested += $currentCount){
    // initialise request parameters.
    $currentCount = calculateCountForCurrentRequest($tweetsRequested, $totalTweetsRequired, $countPerRequest);
    if(!empty($currentCount)){
        $requestParameters = initialiseRequestParameters($username, $currentCount, $maxId);
        // if greater than 0, send a request
        $tweets = $connection->get("statuses/user_timeline", $requestParameters);
        $totalTweetsReturned = sizeof($tweets);

        if ($connection->getLastHttpCode() == 200) {
            // success
            // fetch tweets. avoid redundancy.
            foreach($tweets as $tweet){
                $createDate = new DateTime($tweet->created_at);
                $hourArray = getTweetCreatedHour($tweet->created_at, $currentTimezoneObj);
                incrementStats($stats, $hourArray);
                $maxId = getLowestMaxId($maxId, $tweet->id);
//                printTweet($tweet->id, $tweet->created_at, $hour);
            }
        }else{
            $connException = array(
                'success' => false,
                'message' => 'Last Request to Twitter REST API experienced issues.'
            );
            returnJsonResponse($connException);
        }

        /**
         * An user may not have 500 tweets in total.
         * if returned tweets are less than current count, it
         * is more likely this user do not have 500 tweets.
         * Break the loop here.
         */
        if($totalTweetsReturned < $currentCount){
            break;
        }
    }
}

// At the end of the process, return stats
//$successReturn = $stats;
//$returnData = array();
ksort($stats, SORT_NATURAL);
returnJsonResponse(array_values($stats));

/**
 * * Return tweet created hour at a specific timezone.
 *
 * @param $createAt - created_at field in tweet object return from Twitter REST API.
 * @param DateTimeZone $timezoneObj
 * @return string
 */
function getTweetCreatedHour($createAt, DateTimeZone $timezoneObj){
    $td = new DateTime($createAt);
    $td->setTimezone($timezoneObj);
    $index = $td->format('H');
    $createdHour = $td->format('ga');
    return array(
        'H' => $index,
        'ga' => $createdHour);
}

/**
 * @warning Should be covered by unit test in a real project.
 *
 * @param $tweetsRequested
 * @param $totalTweetsRequired
 * @param $countPerRequest
 * @return int
 */
function calculateCountForCurrentRequest($tweetsRequested, $totalTweetsRequired, $countPerRequest)
{
    $countForCurrentReq = 0;
    // if requested is less than required.
    $diffOnDemand = $totalTweetsRequired - $tweetsRequested;
    if($diffOnDemand){
        if($diffOnDemand < $countPerRequest){
            $countForCurrentReq = $diffOnDemand;
        }else{
            $countForCurrentReq = $countPerRequest;
        }
    }
    return $countForCurrentReq;
}

/**
 * @param $existingMaxId
 * @param $tweetId
 * @return mixed
 */
function getLowestMaxId($existingMaxId, $tweetId)
{
    if(empty($existingMaxId)){
        return $tweetId;
    }
    return $existingMaxId < $tweetId ? $existingMaxId : $tweetId;
}

/**
 * To avoid duplicate tweets, subtract max_id by 1 before
 * request of the next batch.
 *
 * @param $maxId
 * @return mixed
 */
function subtractMaxId($maxId)
{
    return empty($maxId) ? null : --$maxId;
}

/**
 * Initialise request parameters.
 * Add max_id as a parameter only when max_id is valid.
 *
 * @param $username
 * @param $count
 * @param $maxId
 * @return array
 */
function initialiseRequestParameters($username, $count, $maxId)
{
    $requestParameters = array(
        'screen_name'   => $username,
        'count'         => $count
    );
    if(!empty($maxId)){
        $requestParameters['max_id'] = $maxId;
    }
    return $requestParameters;
}

/**
 * @param array $stats
 * @param $hourArray
 */
function incrementStats(array & $stats, $hourArray)
{
    if(isset($stats[$hourArray['H']])){
        $stats[$hourArray['H']]['count']++;
    }else{
        $stats[$hourArray['H']] =
            array(
                'hour'  => $hourArray['ga'],
                'count' => 1
            );
    }
}

/**
 * Output json to screen/request.
 * @param $data
 */
function returnJsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function printTweet($tweetId, $createAt, $hour){
    echo '<pre>'.$tweetId . ' '. $createAt. ' ' .$hour.'</pre>';
}