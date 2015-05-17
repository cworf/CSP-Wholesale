<?php
//====================Twitter===========================

function twitter_get_tweets($twitteruser){

    $cache = get_transient('revoke_twitter');

    if(is_array($cache)&&array_key_exists($twitteruser, $cache))
        return $cache[$twitteruser];

    $consumerkey = _go('twitter_consumerkey');
    $consumersecret = _go('twitter_consumersecret');
    $accesstoken = _go('twitter_accesstoken');
    $accesstokensecret = _go('twitter_accesstokensecret');

    if(empty($consumerkey)||empty($consumersecret)||empty($accesstoken)||empty($accesstokensecret))
        return null;

    $connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
    $tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser);

    if(!is_array($cache))
        $cache = array();
    $cache[$twitteruser] = $tweets;
    set_transient('revoke_twitter',$cache,60);

    return $tweets;
}

function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
    $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
    return $connection;
}

function linkify($status_text){
  // linkify URLs
  $status_text = preg_replace(
    '/(https?:\/\/\S+)/',
    '<a href="\1">\1</a>',
    $status_text
  );

  // linkify twitter users
  $status_text = preg_replace(
    '/(^|\s)@(\w+)/',
    '\1<a href="http://twitter.com/\2">@\2</a>',
    $status_text
  );

  // linkify tags
  $status_text = preg_replace(
    '/(^|\s)#(\w+)/',
    '\1<a href="http://twitter.com/search?q=%23\2&amp;src=hash">#\2</a>',
    $status_text
  );

  return $status_text;
}

function twitter_generate_output($user, $number, $callback='', $step_callback='', $before=false, $after=false){

    $tweets = twitter_get_tweets($user);

    if(is_null($tweets))
        return 'Twitter is not configured.';

    $number = min(20,$number);

    $tweets = array_slice($tweets,0,$number);

    if(!empty($callback))
        return call_user_func($callback,$tweets);

    $output = $before===false?'<div class="tt_twitter"><ul class="twitter">':$before;

        $time = time();
        $last = count($tweets)-1;

        foreach($tweets as $i => $tweet){

            $date = $tweet->created_at;
            $date = date_parse($date);
            $date = mktime(0,0,0,$date['month'],$date['day'],$date['year']);
            $date = $time - $date;

            $seconds = (int)$date;
            $date=floor($date/60);
            $minutes = (int)$date;
            if($minutes){
                $date=floor($date/60);
                $hours = (int)$date;
                if($hours){
                    $date=floor($date/24);
                    $days = (int)$date;
                    if($days){
                        $date=floor($date/7);
                        $weeks = (int)$date;
                        if($weeks)
                            $date = $weeks.' week'.(1===$weeks?'':'s').' ago';
                        else
                            $date = $days.' day'.(1===$days?'':'s').' ago';
                    }
                    else
                        $date = $hours.' hour'.(1===$hours?'':'s').' ago';
                }
                else
                    $date = $minutes.' minute'.(1===$minutes?'':'s').' ago';
            }
            else
                $date = 'less than a minute ago';

            $output .= 
            $step_callback===''?
            '<li'.($i===$last?' class="last"':'').'>'.
                linkify($tweet->text).
                '<span class="date">'.
                    $date.
                '</span>'.
            '</li>'
            :
            call_user_func($step_callback,$i,linkify($tweet->text),$date);

        }

    $output .= $after===false?'</ul></div>':$after;

    return $output;
}