<?php
/**
 * @file include/bbcode.php
 * @brief BBCode related functions for parsing, etc.
 */

require_once('include/oembed.php');
require_once('include/event.php');
require_once('include/zot.php');
require_once('include/hubloc.php');

/* common currency symbols */

$csig = array (
  'ALL' => '&#x4c;&#x65;&#x6b;',
  'AFN' => '&#x60b;',
  'ARS' => '&#x24;',
  'AWG' => '&#x192;',
  'AUD' => '&#x24;',
  'AZN' => '&#x43c;&#x430;&#x43d;',
  'BSD' => '&#x24;',
  'BBD' => '&#x24;',
  'BYR' => '&#x70;&#x2e;',
  'BZD' => '&#x42;&#x5a;&#x24;',
  'BMD' => '&#x24;',
  'BOB' => '&#x24;&#x62;',
  'BAM' => '&#x4b;&#x4d;',
  'BWP' => '&#x50;',
  'BGN' => '&#x43b;&#x432;',
  'BRL' => '&#x52;&#x24;',
  'BND' => '&#x24;',
  'KHR' => '&#x17db;',
  'CAD' => '&#x24;',
  'KYD' => '&#x24;',
  'CLP' => '&#x24;',
  'CNY' => '&#xa5;',
  'COP' => '&#x24;',
  'CRC' => '&#x20a1;',
  'HRK' => '&#x6b;&#x6e;',
  'CUP' => '&#x20b1;',
  'CZK' => '&#x4b;&#x10d;',
  'DKK' => '&#x6b;&#x72;',
  'DOP' => '&#x52;&#x44;&#x24;',
  'XCD' => '&#x24;',
  'EGP' => '&#xa3;',
  'SVC' => '&#x24;',
  'EEK' => '&#x6b;&#x72;',
  'EUR' => '&#x20ac;',
  'FKP' => '&#xa3;',
  'FJD' => '&#x24;',
  'GHC' => '&#xa2;',
  'GIP' => '&#xa3;',
  'GTQ' => '&#x51;',
  'GGP' => '&#xa3;',
  'GYD' => '&#x24;',
  'HNL' => '&#x4c;',
  'HKD' => '&#x24;',
  'HUF' => '&#x46;&#x74;',
  'INR' => '&#x20B9;',
  'ISK' => '&#x6b;&#x72;',
  'IDR' => '&#x52;&#x70;',
  'IRR' => '&#xfdfc;',
  'IMP' => '&#xa3;',
  'ILS' => '&#x20aa;',
  'JMD' => '&#x4a;&#x24;',
  'JPY' => '&#xa5;',
  'JEP' => '&#xa3;',
  'KZT' => '&#x43b;&#x432;',
  'KPW' => '&#x20a9;',
  'KRW' => '&#x20a9;',
  'KGS' => '&#x43b;&#x432;',
  'LAK' => '&#x20ad;',
  'LVL' => '&#x4c;&#x73;',
  'LBP' => '&#xa3;',
  'LRD' => '&#x24;',
  'LTL' => '&#x4c;&#x74;',
  'MKD' => '&#x434;&#x435;&#x43d;',
  'MYR' => '&#x52;&#x4d;',
  'MUR' => '&#x20a8;',
  'MXN' => '&#x24;',
  'MNT' => '&#x20ae;',
  'MZN' => '&#x4d;&#x54;',
  'NAD' => '&#x24;',
  'NPR' => '&#x20a8;',
  'ANG' => '&#x192;',
  'NZD' => '&#x24;',
  'NIO' => '&#x43;&#x24;',
  'NGN' => '&#x20a6;',
  'NOK' => '&#x6b;&#x72;',
  'OMR' => '&#xfdfc;',
  'PKR' => '&#x20a8;',
  'PAB' => '&#x42;&#x2f;&#x2e;',
  'PYG' => '&#x47;&#x73;',
  'PEN' => '&#x53;&#x2f;&#x2e;',
  'PHP' => '&#x20b1;',
  'PLN' => '&#x7a;&#x142;',
  'QAR' => '&#xfdfc;',
  'RON' => '&#x6c;&#x65;&#x69;',
  'RUB' => '&#x440;&#x443;&#x431;',
  'SHP' => '&#xa3;',
  'SAR' => '&#xfdfc;',
  'RSD' => '&#x414;&#x438;&#x43d;&#x2e;',
  'SCR' => '&#x20a8;',
  'SGD' => '&#x24;',
  'SBD' => '&#x24;',
  'SOS' => '&#x53;',
  'ZAR' => '&#x52;',
  'LKR' => '&#x20a8;',
  'SEK' => '&#x6b;&#x72;',
  'CHF' => '&#x43;&#x48;&#x46;',
  'SRD' => '&#x24;',
  'SYP' => '&#xa3;',
  'TWD' => '&#x4e;&#x54;&#x24;',
  'THB' => '&#xe3f;',
  'TTD' => '&#x54;&#x54;&#x24;',
  'TRY' => '&#x20a4;',
  'TVD' => '&#x24;',
  'UAH' => '&#x20b4;',
  'GBP' => '&#xa3;',
  'USD' => '&#x24;',
  'UYU' => '&#x24;&#x55;',
  'UZS' => '&#x43b;&#x432;',
  'VEF' => '&#x42;&#x73;',
  'VND' => '&#x20ab;',
  'YER' => '&#xfdfc;',
  'ZWD' => '&#x5a;&#x24;',
);


$xchange = array();

function load_xchange()
{
	global $xchange;
	if (count($xchange)<1) /* read once per page load, as required */
	{
		$rates = file_get_contents('/tmp/euro.xml');
		$rc=explode("currency='",$rates);
		array_shift($rc);
		foreach ($rc as $rt)
		{
			$rx=explode("'",$rt);
			$cur = $rx[0];
			$rate = $rx[2];
			$xchange[$cur]=$rate;
		}
		$xchange['EUR']=1.0;
	}
}


/* 
	xlt function 
   translate between currencies

get data:
wget -O /tmp/euro.xml http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml

*/

function xlt($amt,$from,$to)
{

	global $csig;
	global $xchange;

	load_xchange();

        $lv = $xchange[$from];
        $rv = $xchange[$to];
        $newamt = $amt/$lv * $rv;
        return ($csig[$to].$newamt);
}

/* 
	xlta function
   translate amount into all other available currencies
   returns array

*/

function xlta($amt,$from)
{

	global $csig;
	global $xchange;

	load_xchange();
        
	$ret = array();

	foreach ($xchange as $k=>$v)
	{
		if ($k!=$from)
		{
	        	$lv = $xchange[$from];
        		$newamt = $amt/$lv * $v;
			$ret[$k] = $csig[$k].number_format($newamt,2,'.','');
		}
	}

	return ($ret);
}       


function tryoembed($match) {
	$url = ((count($match) == 2) ? $match[1] : $match[2]);

	$o = oembed_fetch_url($url);

	if ($o->type == 'error')
		return $match[0];

	$html = oembed_format_object($o);
	return $html; 
}

function tryzrlaudio($match) {
	$link = $match[1];
	$zrl = is_matrix_url($link);
	if($zrl)
		$link = zid($link);

	return '<audio src="' . str_replace(' ','%20',$link) . '" controls="controls"><a href="' . str_replace(' ','%20',$link) . '">' . $link . '</a></audio>';
}

function tryzrlvideo($match) {
	$link = $match[1];
	$zrl = is_matrix_url($link);
	if($zrl)
		$link = zid($link);

	return '<video controls="controls" src="' . str_replace(' ','%20',$link) . '" style="width:100%; max-width:' . get_app()->videowidth . 'px"><a href="' . str_replace(' ','%20',$link) . '">' . $link . '</a></video>';
}

// [noparse][i]italic[/i][/noparse] turns into
// [noparse][ i ]italic[ /i ][/noparse],
// to hide them from parser.

function bb_spacefy($st) {
	$whole_match = $st[0];
	$captured = $st[1];
	$spacefied = preg_replace("/\[(.*?)\]/", "[ $1 ]", $captured);
	$new_str = str_replace($captured, $spacefied, $whole_match);

	return $new_str;
}

// The previously spacefied [noparse][ i ]italic[ /i ][/noparse],
// now turns back and the [noparse] tags are trimmed
// returning [i]italic[/i]

function bb_unspacefy_and_trim($st) {
	//$whole_match = $st[0];
	$captured = $st[1];
	$unspacefied = preg_replace("/\[ (.*?)\ ]/", "[$1]", $captured);

	return $unspacefied;
}


function bb_extract_images($body) {

	$saved_image = array();
	$orig_body = $body;
	$new_body = '';

	$cnt = 0;
	$img_start = strpos($orig_body, '[img');
	$img_st_close = ($img_start !== false ? strpos(substr($orig_body, $img_start), ']') : false);
	$img_end = ($img_start !== false ? strpos(substr($orig_body, $img_start), '[/img]') : false);
	while(($img_st_close !== false) && ($img_end !== false)) {

		$img_st_close++; // make it point to AFTER the closing bracket
		$img_end += $img_start;

		if(! strcmp(substr($orig_body, $img_start + $img_st_close, 5), 'data:')) {
			// This is an embedded image

			$saved_image[$cnt] = substr($orig_body, $img_start + $img_st_close, $img_end - ($img_start + $img_st_close));
			$new_body = $new_body . substr($orig_body, 0, $img_start) . '[$#saved_image' . $cnt . '#$]';

			$cnt++;
		}
		else
			$new_body = $new_body . substr($orig_body, 0, $img_end + strlen('[/img]'));

		$orig_body = substr($orig_body, $img_end + strlen('[/img]'));

		if($orig_body === false) // in case the body ends on a closing image tag
			$orig_body = '';

		$img_start = strpos($orig_body, '[img');
		$img_st_close = ($img_start !== false ? strpos(substr($orig_body, $img_start), ']') : false);
		$img_end = ($img_start !== false ? strpos(substr($orig_body, $img_start), '[/img]') : false);
	}

	$new_body = $new_body . $orig_body;

	return array('body' => $new_body, 'images' => $saved_image);
}



function bb_replace_images($body, $images) {

	$newbody = $body;
	$cnt = 0;

	if(! $images)
		return $newbody;

	foreach($images as $image) {
		// We're depending on the property of 'foreach' (specified on the PHP website) that
		// it loops over the array starting from the first element and going sequentially
		// to the last element
		$newbody = str_replace('[$#saved_image' . $cnt . '#$]', '<img src="' . $image .'" alt="' . t('Image/photo') . '" />', $newbody);
		$cnt++;
	}
//	logger('replace_images: ' . $newbody);
	return $newbody;
}

function bb_replace_forms($body, $forms) {
        $newbody = $body;
        $cnt = 0;

        if(! $forms)
                return $newbody;

        foreach($forms as $form) {
                $newbody = str_replace('[$#saved_form' . $cnt . '#$]', $form, $newbody);
                $cnt++;
        }
        return $newbody;
}


/**
 * @brief Parses crypt BBCode.
 *
 * @param array $match
 * @return string HTML code
 */
function bb_parse_crypt($match) {

	$matches = array();
	$attributes = $match[1];

	$algorithm = "";

	preg_match("/alg='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$algorithm = $matches[1];

	preg_match("/alg=\&quot\;(.*?)\&quot\;/ism", $attributes, $matches);
	if ($matches[1] != "")
		$algorithm = $matches[1];

	$hint = "";

	preg_match("/hint='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$hint = $matches[1];
	preg_match("/hint=\&quot\;(.*?)\&quot\;/ism", $attributes, $matches);
	if ($matches[1] != "")
		$hint = $matches[1];

	$x = random_string();

	$Text = '<br /><div id="' . $x . '"><img src="' . z_root() . '/images/lock_icon.gif" onclick="red_decrypt(\'' . $algorithm . '\',\'' . $hint . '\',\'' . $match[2] . '\',\'#' . $x . '\');" alt="' . t('Encrypted content') . '" title="' . t('Encrypted content') . '" /></div><br />';

	return $Text;
}

function bb_parse_app($match) {
	require_once('include/apps.php');

	$app = app_decode($match[1]);
	if ($app)
		return app_render($app);
}

function bb_parse_element($match) {
	$j = json_decode(base64url_decode($match[1]),true);

	if ($j && local_channel()) {
		$text = sprintf( t('Install %s element: '), translate_design_element($j['type'])) . $j['pagetitle'];
		$o = EOL . '<a href="#" onclick="importElement(\'' . $match[1] . '\'); return false;" >' . $text . '</a>' . EOL;
	}
	else {
		$text = sprintf( t('This post contains an installable %s element, however you lack permissions to install it on this site.' ), translate_design_element($j['type'])) . $j['pagetitle'];
		$o = EOL . $text . EOL;
	}

	return $o;
}

function translate_design_element($type) {
	switch($type) {
		case 'webpage':
			$ret = t('webpage');
			break;
		case 'layout':
			$ret =  t('layout');
			break;
		case 'block':
			$ret =  t('block');
			break;
		case 'menu':
			$ret =  t('menu');
			break;
	}

	return $ret;
}

/**
 * @brief Returns an QR-code image from a value given in $match[1].
 *
 * @param array $match
 * @return string HTML img with QR-code of $match[1]
 */
function bb_qr($match) {
	return '<img class="zrl" src="' . z_root() . '/photo/qr?f=&qr=' . urlencode($match[1]) . '" alt="' . t('QR code') . '" title="' . htmlspecialchars($match[1],ENT_QUOTES,'UTF-8') . '" />';
}


function bb_ShareAttributes($match) {

	$matches = array();
	$attributes = $match[1];

	$author = "";
	preg_match("/author='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$author = urldecode($matches[1]);

	$link = "";
	preg_match("/link='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$link = $matches[1];

	$avatar = "";
	preg_match("/avatar='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$avatar = $matches[1];

	$profile = "";
	preg_match("/profile='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$profile = $matches[1];

	$posted = "";
	preg_match("/posted='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$posted = $matches[1];

	// message_id is never used, do we still need it?
	$message_id = "";
	preg_match("/message_id='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$message_id = $matches[1];


	/** @FIXME - this should really be a wall-item-ago so it will get updated on the client */
	$reldate = (($posted) ? relative_date($posted) : ''); 

	$headline = '<div class="shared_container"> <div class="shared_header">';

	if ($avatar != "")
		$headline .= '<a href="' . zid($profile) . '" ><img src="' . $avatar . '" alt="' . $author . '" height="32" width="32" /></a>';

	// Bob Smith wrote the following post 2 hours ago

	$fmt = sprintf( t('%1$s wrote the following %2$s %3$s'),
		'<a href="' . zid($profile) . '" >' . $author . '</a>',
		'<a href="' . zid($link) . '" >' . t('post') . '</a>',
		$reldate
	);

	$headline .= '<span>' . $fmt . '</span></div>';

	$text = $headline . '<div class="reshared-content">' . trim($match[2]) . '</div></div>';

	return $text;
}

function bb_location($match) {
	// not yet implemented
}

/**
 * @brief Returns an iframe from $match[1].
 *
 * @param array $match
 * @return string HTML iframe with content of $match[1]
 */
function bb_iframe($match) {
	$a = get_app();

	$sandbox = ((strpos($match[1], $a->get_hostname())) ? ' sandbox="allow-scripts" ' : '');

	return '<iframe ' . $sandbox . ' src="' . $match[1] . '" width="' . $a->videowidth . '" height="' . $a->videoheight . '"><a href="' . $match[1] . '">' . $match[1] . '</a></iframe>';
}

function bb_ShareAttributesSimple($match) {

	$matches = array();
	$attributes = $match[1];

	$author = "";
	preg_match("/author='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$author = html_entity_decode($matches[1],ENT_QUOTES,'UTF-8');

	preg_match('/author="(.*?)"/ism', $attributes, $matches);
	if ($matches[1] != "")
		$author = $matches[1];

	$profile = "";
	preg_match("/profile='(.*?)'/ism", $attributes, $matches);
	if ($matches[1] != "")
		$profile = $matches[1];

	preg_match('/profile="(.*?)"/ism', $attributes, $matches);
	if ($matches[1] != "")
		$profile = $matches[1];

	$text = html_entity_decode("&#x2672; ", ENT_QUOTES, 'UTF-8') . ' <a href="' . $profile . '">' . $author . '</a>: div class="reshared-content">' . $match[2] . '</div>';

	return($text);
}

function rpost_callback($match) {
	if ($match[2]) {
		return str_replace($match[0], get_rpost_path(get_app()->get_observer()) . '&title=' . urlencode($match[2]) . '&body=' . urlencode($match[3]), $match[0]);
	} else {
		return str_replace($match[0], get_rpost_path(get_app()->get_observer()) . '&body=' . urlencode($match[3]), $match[0]);
	}
}

function bb_map_coords($match) {
	// the extra space in the following line is intentional
	return str_replace($match[0],'<div class="map"  >' . generate_map(str_replace('/',' ',$match[1])) . '</div>', $match[0]);
}

function bb_map_location($match) {
	// the extra space in the following line is intentional
	return str_replace($match[0],'<div class="map"  >' . generate_named_map($match[1]) . '</div>', $match[0]);
}

function bb_opentag($match) {
	$rnd = mt_rand();
	return "<br /><div onclick=\"openClose('opendiv-" . $rnd . "');return false;\" class=\"fakelink\">" . $match[1] . "</div><div id=\"opendiv-" . $rnd . "\" style=\"display: none;\">" . $match[2] . "</div>";
}

/**
 * @brief Sanitize style properties from BBCode to HTML.
 *
 * @param array $input
 * @return string A HTML span tag with the styles.
 */
function bb_sanitize_style($input) {
	// whitelist array: property => limits (0 = no limitation)
	$w = array(
			// color properties
			"color"            => 0,
			"background-color" => 0,
			// box properties
			"padding"          => array("px"=>100, "%"=>0, "em"=>2, "ex"=>2, "mm"=>0, "cm"=>0, "in"=>0, "pt"=>0, "pc"=>0),
			"margin"           => array("px"=>100, "%"=>0, "em"=>2, "ex"=>2, "mm"=>0, "cm"=>0, "in"=>0, "pt"=>0, "pc"=>0),
			"border"           => array("px"=>100, "%"=>0, "em"=>2, "ex"=>2, "mm"=>0, "cm"=>0, "in"=>0, "pt"=>0, "pc"=>0),
			"float"            => 0,
			"clear"            => 0,
			// text properties
			"text-decoration"  => 0,
	);

	$css = array();
	$css_string = $input[1];
	$a = explode(';', $css_string);

	foreach($a as $parts){
		list($k, $v) = explode(':', $parts);
		$css[ trim($k) ] = trim($v);
	}

	// sanitize properties
	$b = array_merge(array_diff_key($css, $w), array_diff_key($w, $css));
	$css = array_diff_key($css, $b);
	$css_string_san = '';

	foreach ($css as $key => $value) {
		if ($w[$key] != null) {
			foreach ($w[$key] as $limit_key => $limit_value) {
				//sanitize values
				if (strpos($value, $limit_key)) {
					$value = preg_replace_callback(
						"/(\S.*?)$limit_key/ism",
						function($match) use($limit_value, $limit_key) {
							if ($match[1] > $limit_value) {
								return $limit_value . $limit_key;
							} else {
								return $match[1] . $limit_key;
							}
						},
						$value
					);
				}
			}
		}
		$css_string_san .= $key . ":" . $value ."; ";
	}

	return '<span style="' . $css_string_san . '">' . $input[2] . '</span>';
}

	// BBcode 2 HTML was written by WAY2WEB.net
	// extended to work with Mistpark/Friendica/Red - Mike Macgirvin

function bbcode($Text, $preserve_nl = false, $tryoembed = true) {

	$a = get_app();

	// Move all spaces out of the tags
	// ....Uhm why?
	// This is basically doing a trim() on the stuff in between tags, but it messes up
	// carefully crafted bbcode and especially other pre-formatted code. 
	// Commenting out until we come up with a use case where it's needed. Then let's try and
	// special case rather than a heavy-handed approach like this. 

//	$Text = preg_replace("/\[(\w*)\](\s*)/ism", '$2[$1]', $Text);
//	$Text = preg_replace("/(\s*)\[\/(\w*)\]/ism", '[/$2]$1', $Text);

	// Hide all [noparse] contained bbtags by spacefying them
	if (strpos($Text,'[noparse]') !== false) {
		$Text = preg_replace_callback("/\[noparse\](.*?)\[\/noparse\]/ism", 'bb_spacefy',$Text);
	}
	if (strpos($Text,'[nobb]') !== false) {
		$Text = preg_replace_callback("/\[nobb\](.*?)\[\/nobb\]/ism", 'bb_spacefy',$Text);
	}
	if (strpos($Text,'[pre]') !== false) {
		$Text = preg_replace_callback("/\[pre\](.*?)\[\/pre\]/ism", 'bb_spacefy',$Text);
	}



	// If we find any event code, turn it into an event.
	// After we're finished processing the bbcode we'll
	// replace all of the event code with a reformatted version.

	$ev = bbtoevent($Text);

	// process [observer] tags before we do anything else because we might
	// be stripping away stuff that then doesn't need to be worked on anymore

	if(get_config('system','item_cache'))
		$observer = false;
	else
		$observer = $a->get_observer();

	if ((strpos($Text,'[/observer]') !== false) || (strpos($Text,'[/rpost]') !== false)) {
		if ($observer) {
			$Text = preg_replace("/\[observer\=1\](.*?)\[\/observer\]/ism", '$1', $Text);
			$Text = preg_replace("/\[observer\=0\].*?\[\/observer\]/ism", '', $Text);
			$Text = preg_replace_callback("/\[rpost(=(.*?))?\](.*?)\[\/rpost\]/ism", 'rpost_callback', $Text);
		} else {
			$Text = preg_replace("/\[observer\=1\].*?\[\/observer\]/ism", '', $Text);
			$Text = preg_replace("/\[observer\=0\](.*?)\[\/observer\]/ism", '$1', $Text);
			$Text = preg_replace("/\[rpost(=.*?)?\](.*?)\[\/rpost\]/ism", '', $Text);
		}
	}

	if(get_config('system','item_cache'))
		$channel = false;
	else
		$channel = $a->get_channel();

	if (strpos($Text,'[/channel]') !== false) {
		if ($channel) {
			$Text = preg_replace("/\[channel\=1\](.*?)\[\/channel\]/ism", '$1', $Text);
			$Text = preg_replace("/\[channel\=0\].*?\[\/channel\]/ism", '', $Text);
		} else {
			$Text = preg_replace("/\[channel\=1\].*?\[\/channel\]/ism", '', $Text);
			$Text = preg_replace("/\[channel\=0\](.*?)\[\/channel\]/ism", '$1', $Text);
		}
	}



/*

Example Array

Array
(
    [command] => list-product
    [signature] => b83c415f9f54fb32c979a4951d084e24edbf22b3
    [sku] => Test1
    [title] => This is a test
    [description] => This is a test description.
    [images] => Array
        (
            [0] => https://rez.waitman.net/photo/0917a3b2e9844f3fca16552a6e78399d-1.jpg?f=&_u=150715063820
        )

    [price] => 54.00
    [currency] => USD
    [options] => Array
        (
            [Top Size] => Array
                (
                    [0] => XS|Top-XS|0
                    [1] => S|Top-S|0
                    [2] => M|Top-M|0
                    [3] => L|Top-L|0
                    [4] => XL|Top-XL|2
                )

            [Color] => Array
                (
                    [0] => Red|Red|0
                    [1] => Green|Green|0
                    [2] => Blue|Blue|0
                )

        )

)
*/



$saved_forms = array();

// json extensions
/* 

todo - add key to config 
todo - use template to generate product info
todo - change addcart URL
todo - localization
todo - multiple images, image handling

idea to validate submitted data using signature.

replace checksum in passed array with key, then calculate hash to compare

ex:

$checksum = $json['signature'];
$json['signature']=$userkey;
if ($check != hash("whirlpool",serialize($json),false);

*/

	global $csig;
	global $xchange;

        if (strpos($Text,'[json]') !== false)
        {
                $tmpva=explode('[json]',$Text);

                if (count($tmpva)>1)
                {
                        for ($i=1;$i<count($tmpva);$i++)
                        {
                                $tmpra=explode('[/json]',$tmpva[$i]);
                                $json = $tmpra[0];
                                
                                $tmptxt = '';

                                $vjson = json_decode(html_entity_decode($json), true);
                                
                                switch ($vjson['command'])
                                {
					case 'list-product':
					if (count($vjson['images'])>0)
					{
						
						$tmptxt .= '<div style="overflow:hidden;width:100%;">';
						foreach ($vjson['images'] as $vimg)
						{
							$tmptxt.='<a href="'.$vimg.'" onclick="$.colorbox({href: '."'".$vimg."'".'}); return false;"><img src="'.$vimg.'" style="max-width:200px;height:auto;float:left;padding-right:1px;" /></a>';
						}
						$tmptxt .= '
</div><div clear="both"></div>';
					}
					$tmptxt .= '<h2>'.$vjson['title'].'</h2>
<p>SKU: '.$vjson['sku'].'</p>
<p>'.$vjson['description'].'</p>
<form method="post" action="addcart.php">
<input type="hidden" name="sku" value="'.$vjson['sku'].'" />
<input type="hidden" name="signature" value="'.$vjson['signature'].'" />
';

					if (count($vjson['options'])>0)
					{
						$tmptxt .= '<table border="0" cellspacing="0" cellpadding="3">
';
						foreach ($vjson['options'] as $k=>$v)
						{
							$tmptxt.= '<tr><td>'.$k.':</td><td><select name="'.$k.'">';
							foreach ($v as $l=>$m)
							{
								$tmptxt.='<option value="'.$m.'">';
								$jm=explode('|',$m);
								if ($jm[2]>0)
								{
									$addprice = ' (+$'.$jm[2].')';
								} 
									elseif ($jm[2]<0)
								{
									$addprice = ' (-$'.$jm[2].')';
								} 
									else
								{
									$addprice = '';
								}
														
								$tmptxt.= $jm[0].$addprice.'</option>';
							}
							$tmptxt .= '</select></tr>';
						}
						if (count($vjson['options'])>0)
						{
							$tmptxt .= '</table>';
						}
						$tmptxt .= '<p>Price: '.$csig[$vjson['currency']].number_format(floatval($vjson['price']),2,'.','').' '.$vjson['currency'].'</p>
';

$cur = xlta($vjson['price'],$vjson['currency']);

$cols=6;
$tmptxt .= '<table border="0" cellspacing="0" cellpadding="3" width="100%">
';

$co = array();
$tc = 0;

foreach ($cur as $k=>$v)
{
	$co[$tc++][]='<td style="font-size:80%;text-align:right;">'.$v.' '.$k.'</td>';
	if ($tc==$cols)
	{
		$tc=0;
	}
}
$maxrow = 0;
for ($ij=0;$ij<$cols;$ij++)
{
	if (count($co)>$maxrow)
	{
		$maxrow=count($co);
	}
}
for ($ij=0;$ij<$maxrow;$ij++)
{
	$tmptxt .= '<tr>';
	for ($in=0;$in<$cols;$in++)
	{
		$tmptxt .= $co[$in][$ij];
	}
	$tmptxt .= '</tr>';
}
$tmptxt .= '
</table>
';
$tmptxt .= '
<input type="submit" name="addproduct" value="Purchase This Item" />
</form>
';
					}

					break;
				}
													
				$tmpva[$i]='[$#saved_form'.count($saved_forms).'#$]';
				$saved_forms[]=$tmptxt;
                        }
                        $Text = join('',$tmpva);
                }
        }







	$x = bb_extract_images($Text);
	$Text = $x['body'];
	$saved_images = $x['images'];

	$Text = str_replace(array('[baseurl]','[sitename]'),array(z_root(),get_config('system','sitename')),$Text);


	// Replace any html brackets with HTML Entities to prevent executing HTML or script
	// Don't use strip_tags here because it breaks [url] search by replacing & with amp

	$Text = str_replace("<", "&lt;", $Text);
	$Text = str_replace(">", "&gt;", $Text);


	// Convert new line chars to html <br /> tags

	// nlbr seems to be hopelessly messed up
	//	$Text = nl2br($Text);

	// We'll emulate it.

	$Text = str_replace("\r\n", "\n", $Text);
	$Text = str_replace(array("\r", "\n"), array('<br />', '<br />'), $Text);

	if ($preserve_nl)
		$Text = str_replace(array("\n", "\r"), array('', ''), $Text);


	$Text = str_replace(array("\t", "  "), array("&nbsp;&nbsp;&nbsp;&nbsp;", "&nbsp;&nbsp;"), $Text);

	// Set up the parameters for a URL search string
	$URLSearchString = "^\[\]";
	// Set up the parameters for a MAIL search string
	$MAILSearchString = $URLSearchString;

	// replace [observer.baseurl]
	if ($observer) {
		$s1 = '<span class="bb_observer" title="' . t('Different viewers will see this text differently') . '">';
		$s2 = '</span>';
		$obsBaseURL = $observer['xchan_connurl'];
		$obsBaseURL = preg_replace("/\/poco\/.*$/", '', $obsBaseURL);
		$Text = str_replace('[observer.baseurl]', $obsBaseURL, $Text);
		$Text = str_replace('[observer.url]',$observer['xchan_url'], $Text);
		$Text = str_replace('[observer.name]',$s1 . $observer['xchan_name'] . $s2, $Text);
		$Text = str_replace('[observer.address]',$s1 . $observer['xchan_addr'] . $s2, $Text);
		$Text = str_replace('[observer.webname]', substr($observer['xchan_addr'],0,strpos($observer['xchan_addr'],'@')), $Text);
		$Text = str_replace('[observer.photo]',$s1 . '[zmg]'.$observer['xchan_photo_l'].'[/zmg]' . $s2, $Text);
	} else {
		$Text = str_replace('[observer.baseurl]', '', $Text);
		$Text = str_replace('[observer.url]','', $Text);
		$Text = str_replace('[observer.name]','', $Text);
		$Text = str_replace('[observer.address]','', $Text);
		$Text = str_replace('[observer.webname]','',$Text);
		$Text = str_replace('[observer.photo]','', $Text);
	}



	// Perform URL Search

	$urlchars = '[a-zA-Z0-9\:\/\-\?\&\;\.\=\@\_\~\#\%\$\!\+\,\@]';

	if (strpos($Text,'http') !== false) {
		$Text = preg_replace("/([^\]\='".'"'."\/]|^|\#\^)(https?\:\/\/$urlchars+)/ism", '$1<a href="$2" >$2</a>', $Text);
	}

	if (strpos($Text,'[/qr]') !== false) {
		$Text = preg_replace_callback("/\[qr\](.*?)\[\/qr\]/ism", 'bb_qr', $Text);
	}

	if (strpos($Text,'[/share]') !== false) {
		$Text = preg_replace_callback("/\[share(.*?)\](.*?)\[\/share\]/ism", 'bb_ShareAttributes', $Text);
	}
	if($tryoembed) {
		if (strpos($Text,'[/url]') !== false) {
			$Text = preg_replace_callback("/\[url\]([$URLSearchString]*)\[\/url\]/ism", 'tryoembed', $Text);
		}
	}
	if (strpos($Text,'[/url]') !== false) {
		$Text = preg_replace("/\#\^\[url\]([$URLSearchString]*)\[\/url\]/ism", '<span class="bookmark-identifier">#^</span><a class="bookmark" href="$1" >$1</a>', $Text);
		$Text = preg_replace("/\#\^\[url\=([$URLSearchString]*)\](.*?)\[\/url\]/ism", '<span class="bookmark-identifier">#^</span><a class="bookmark" href="$1" >$2</a>', $Text);
		$Text = preg_replace("/\[url\]([$URLSearchString]*)\[\/url\]/ism", '<a href="$1" >$1</a>', $Text);
		$Text = preg_replace("/\[url\=([$URLSearchString]*)\](.*?)\[\/url\]/ism", '<a href="$1" >$2</a>', $Text);
	}
	if (strpos($Text,'[/zrl]') !== false) {
		$Text = preg_replace("/\#\^\[zrl\]([$URLSearchString]*)\[\/zrl\]/ism", '<span class="bookmark-identifier">#^</span><a class="zrl bookmark" href="$1" >$1</a>', $Text);
		$Text = preg_replace("/\#\^\[zrl\=([$URLSearchString]*)\](.*?)\[\/zrl\]/ism", '<span class="bookmark-identifier">#^</span><a class="zrl bookmark" href="$1" >$2</a>', $Text);
		$Text = preg_replace("/\[zrl\]([$URLSearchString]*)\[\/zrl\]/ism", '<a class="zrl" href="$1" >$1</a>', $Text);
		$Text = preg_replace("/\[zrl\=([$URLSearchString]*)\](.*?)\[\/zrl\]/ism", '<a class="zrl" href="$1" >$2</a>', $Text);
	}
	// Perform MAIL Search
	if (strpos($Text,'[/mail]') !== false) {
		$Text = preg_replace("/\[mail\]([$MAILSearchString]*)\[\/mail\]/", '<a href="mailto:$1">$1</a>', $Text);
		$Text = preg_replace("/\[mail\=([$MAILSearchString]*)\](.*?)\[\/mail\]/", '<a href="mailto:$1">$2</a>', $Text);
	}

	// leave open the posibility of [map=something]
	// this is replaced in prepare_body() which has knowledge of the item location

	if (strpos($Text,'[/map]') !== false) {
		$Text = preg_replace_callback("/\[map\](.*?)\[\/map\]/ism", 'bb_map_location', $Text);
	}
	if (strpos($Text,'[map=') !== false) {
		$Text = preg_replace_callback("/\[map=(.*?)\]/ism", 'bb_map_coords', $Text);
	}
	if (strpos($Text,'[map]') !== false) {
		$Text = preg_replace("/\[map\]/", '<div class="map"></div>', $Text);
	}

	// Check for bold text
	if (strpos($Text,'[b]') !== false) {
		$Text = preg_replace("(\[b\](.*?)\[\/b\])ism", '<strong>$1</strong>', $Text);
	}
	// Check for Italics text
	if (strpos($Text,'[i]') !== false) {
		$Text = preg_replace("(\[i\](.*?)\[\/i\])ism", '<em>$1</em>', $Text);
	}
	// Check for Underline text
	if (strpos($Text,'[u]') !== false) {
		$Text = preg_replace("(\[u\](.*?)\[\/u\])ism", '<u>$1</u>', $Text);
	}
	// Check for strike-through text
	if (strpos($Text,'[s]') !== false) {
		$Text = preg_replace("(\[s\](.*?)\[\/s\])ism", '<strike>$1</strike>', $Text);
	}
	// Check for over-line text
	if (strpos($Text,'[o]') !== false) {
		$Text = preg_replace("(\[o\](.*?)\[\/o\])ism", '<span class="overline">$1</span>', $Text);
	}
	if (strpos($Text,'[sup]') !== false) {
		$Text = preg_replace("(\[sup\](.*?)\[\/sup\])ism", '<sup>$1</sup>', $Text);
	}
	if (strpos($Text,'[sub]') !== false) {
		$Text = preg_replace("(\[sub\](.*?)\[\/sub\])ism", '<sub>$1</sub>', $Text);
	}

	// Check for colored text
	if (strpos($Text,'[/color]') !== false) {
		$Text = preg_replace("(\[color=(.*?)\](.*?)\[\/color\])ism", "<span style=\"color: $1;\">$2</span>", $Text);
	}
	// Check for sized text
	// [size=50] --> font-size: 50px (with the unit).
	if (strpos($Text,'[/size]') !== false) {
		$Text = preg_replace("(\[size=(\d*?)\](.*?)\[\/size\])ism", "<span style=\"font-size: $1px;\">$2</span>", $Text);
		$Text = preg_replace("(\[size=(.*?)\](.*?)\[\/size\])ism", "<span style=\"font-size: $1;\">$2</span>", $Text);
	}
	// Check for h1
	if (strpos($Text,'[h1]') !== false) {
		$Text = preg_replace("(\[h1\](.*?)\[\/h1\])ism",'<h1>$1</h1>',$Text);
	}
	// Check for h2
	if (strpos($Text,'[h2]') !== false) {
		$Text = preg_replace("(\[h2\](.*?)\[\/h2\])ism",'<h2>$1</h2>',$Text);
	}
	// Check for h3
	if (strpos($Text,'[h3]') !== false) {
		$Text = preg_replace("(\[h3\](.*?)\[\/h3\])ism",'<h3>$1</h3>',$Text);
	}
	// Check for h4
	if (strpos($Text,'[h4]') !== false) {
		$Text = preg_replace("(\[h4\](.*?)\[\/h4\])ism",'<h4>$1</h4>',$Text);
	}
	// Check for h5
	if (strpos($Text,'[h5]') !== false) {
		$Text = preg_replace("(\[h5\](.*?)\[\/h5\])ism",'<h5>$1</h5>',$Text);
	}
	// Check for h6
	if (strpos($Text,'[h6]') !== false) {
		$Text = preg_replace("(\[h6\](.*?)\[\/h6\])ism",'<h6>$1</h6>',$Text);
	}
	// Check for table of content without params
	if (strpos($Text,'[toc]') !== false) {
		$Text = preg_replace("/\[toc\]/ism",'<ul id="toc"></ul>',$Text);
	}
	// Check for table of content with params
	if (strpos($Text,'[toc') !== false) {
		$Text = preg_replace("/\[toc([^\]]+?)\]/ism",'<ul$1></ul>',$Text);
	}
	// Check for centered text
	if (strpos($Text,'[/center]') !== false) {
	$Text = preg_replace("(\[center\](.*?)\[\/center\])ism", "<div style=\"text-align:center;\">$1</div>", $Text);
	}
	// Check for list text
	$Text = str_replace("[*]", "<li>", $Text);

 	// handle nested lists
	$endlessloop = 0;

	while ((((strpos($Text, "[/list]") !== false) && (strpos($Text, "[list") !== false)) ||
			((strpos($Text, "[/ol]") !== false) && (strpos($Text, "[ol]") !== false)) ||
			((strpos($Text, "[/ul]") !== false) && (strpos($Text, "[ul]") !== false)) ||
			((strpos($Text, "[/li]") !== false) && (strpos($Text, "[li]") !== false))) && (++$endlessloop < 20)) {
		$Text = preg_replace("/\[list\](.*?)\[\/list\]/ism", '<ul class="listbullet" style="list-style-type: circle;">$1</ul>', $Text);
		$Text = preg_replace("/\[list=\](.*?)\[\/list\]/ism", '<ul class="listnone" style="list-style-type: none;">$1</ul>', $Text);
		$Text = preg_replace("/\[list=1\](.*?)\[\/list\]/ism", '<ul class="listdecimal" style="list-style-type: decimal;">$1</ul>', $Text);
		$Text = preg_replace("/\[list=((?-i)i)\](.*?)\[\/list\]/ism",'<ul class="listlowerroman" style="list-style-type: lower-roman;">$2</ul>', $Text);
		$Text = preg_replace("/\[list=((?-i)I)\](.*?)\[\/list\]/ism", '<ul class="listupperroman" style="list-style-type: upper-roman;">$2</ul>', $Text);
		$Text = preg_replace("/\[list=((?-i)a)\](.*?)\[\/list\]/ism", '<ul class="listloweralpha" style="list-style-type: lower-alpha;">$2</ul>', $Text);
		$Text = preg_replace("/\[list=((?-i)A)\](.*?)\[\/list\]/ism", '<ul class="listupperalpha" style="list-style-type: upper-alpha;">$2</ul>', $Text);
		$Text = preg_replace("/\[ul\](.*?)\[\/ul\]/ism", '<ul class="listbullet" style="list-style-type: circle;">$1</ul>', $Text);
		$Text = preg_replace("/\[ol\](.*?)\[\/ol\]/ism", '<ul class="listdecimal" style="list-style-type: decimal;">$1</ul>', $Text);
		$Text = preg_replace("/\[li\](.*?)\[\/li\]/ism", '<li>$1</li>', $Text);
	}
	if (strpos($Text,'[th]') !== false) {
		$Text = preg_replace("/\[th\](.*?)\[\/th\]/sm", '<th>$1</th>', $Text);
	}
	if (strpos($Text,'[td]') !== false) {
		$Text = preg_replace("/\[td\](.*?)\[\/td\]/sm", '<td>$1</td>', $Text);
	}
	if (strpos($Text,'[tr]') !== false) {
		$Text = preg_replace("/\[tr\](.*?)\[\/tr\]/sm", '<tr>$1</tr>', $Text);
	}
	if (strpos($Text,'[/table]') !== false) {
		$Text = preg_replace("/\[table\](.*?)\[\/table\]/sm", '<table>$1</table>', $Text);
		$Text = preg_replace("/\[table border=1\](.*?)\[\/table\]/sm", '<table border="1" >$1</table>', $Text);
		$Text = preg_replace("/\[table border=0\](.*?)\[\/table\]/sm", '<table border="0" >$1</table>', $Text);
	}
	$Text = str_replace('</tr><br /><tr>', "</tr>\n<tr>", $Text);
	$Text = str_replace('[hr]', '<hr />', $Text);

	// This is actually executed in prepare_body()

	$Text = str_replace('[nosmile]', '', $Text);

	// Check for font change text
	if (strpos($Text,'[/font]') !== false) {
		$Text = preg_replace("/\[font=(.*?)\](.*?)\[\/font\]/sm", "<span style=\"font-family: $1;\">$2</span>", $Text);
	}

	// Declare the format for [code] layout
	$CodeLayout = '<code>$1</code>';

	// Check for [code] text
	if (strpos($Text,'[code]') !== false) {
		$Text = preg_replace("/\[code\](.*?)\[\/code\]/ism", "$CodeLayout", $Text);
	}

	// Declare the format for [spoiler] layout
	$SpoilerLayout = '<blockquote class="spoiler">$1</blockquote>';

	// Check for [spoiler] text
	// handle nested quotes
	$endlessloop = 0;
	while ((strpos($Text, "[/spoiler]") !== false) and (strpos($Text, "[spoiler]") !== false) and (++$endlessloop < 20))
		$Text = preg_replace("/\[spoiler\](.*?)\[\/spoiler\]/ism", "$SpoilerLayout", $Text);

	// Check for [spoiler=Author] text

	$t_wrote = t('$1 spoiler');

	// handle nested quotes
	$endlessloop = 0;
	while ((strpos($Text, "[/spoiler]")!== false)  and (strpos($Text, "[spoiler=") !== false) and (++$endlessloop < 20))
		$Text = preg_replace("/\[spoiler=[\"\']*(.*?)[\"\']*\](.*?)\[\/spoiler\]/ism",
			"<br /><strong class=".'"spoiler"'.">" . $t_wrote . "</strong><blockquote class=".'"spoiler"'.">$2</blockquote>",
			$Text);


	$endlessloop = 0;
	while ((strpos($Text, "[/open]")!== false)  and (strpos($Text, "[open=") !== false) and (++$endlessloop < 20)) {
		$Text = preg_replace_callback("/\[open=(.*?)\](.*?)\[\/open\]/ism", 'bb_opentag', $Text);
	}


	// Declare the format for [quote] layout
	$QuoteLayout = '<blockquote>$1</blockquote>';

	// Check for [quote] text
	// handle nested quotes
	$endlessloop = 0;
	while ((strpos($Text, "[/quote]") !== false) and (strpos($Text, "[quote]") !== false) and (++$endlessloop < 20))
		$Text = preg_replace("/\[quote\](.*?)\[\/quote\]/ism", "$QuoteLayout", $Text);

	// Check for [quote=Author] text

	$t_wrote = t('$1 wrote:');

	// handle nested quotes
	$endlessloop = 0;
	while ((strpos($Text, "[/quote]")!== false)  and (strpos($Text, "[quote=") !== false) and (++$endlessloop < 20))
		$Text = preg_replace("/\[quote=[\"\']*(.*?)[\"\']*\](.*?)\[\/quote\]/ism",
			"<br /><strong class=".'"author"'.">" . $t_wrote . "</strong><blockquote>$2</blockquote>",
			$Text);

	// Images
	// [img]pathtoimage[/img]
	if (strpos($Text,'[/img]') !== false) {
		$Text = preg_replace("/\[img\](.*?)\[\/img\]/ism", '<img style="max-width=100%;" src="$1" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/zmg]') !== false) {
		$Text = preg_replace("/\[zmg\](.*?)\[\/zmg\]/ism", '<img class="zrl" style="max-width=100%;" src="$1" alt="' . t('Image/photo') . '" />', $Text);
	}

	// [img float={left, right}]pathtoimage[/img]
	if (strpos($Text,'[/img]') !== false) {
		$Text = preg_replace("/\[img float=left\](.*?)\[\/img\]/ism", '<img style="max-width=100%;" src="$1" style="float: left;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/img]') !== false) {
		$Text = preg_replace("/\[img float=right\](.*?)\[\/img\]/ism", '<img style="max-width=100%;" src="$1" style="float: right;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/zmg]') !== false) {
		$Text = preg_replace("/\[zmg float=left\](.*?)\[\/zmg\]/ism", '<img style="max-width=100%;" class="zrl" src="$1" style="float: left;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/zmg]') !== false) {
		$Text = preg_replace("/\[zmg float=right\](.*?)\[\/zmg\]/ism", '<img style="max-width=100%;" class="zrl" src="$1" style="float: right;" alt="' . t('Image/photo') . '" />', $Text);
	}

	// [img=widthxheight]pathtoimage[/img]
	if (strpos($Text,'[/img]') !== false) {
		$Text = preg_replace("/\[img\=([0-9]*)x([0-9]*)\](.*?)\[\/img\]/ism", '<img src="$3" style="width: 100%; max-width: $1px;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/zmg]') !== false) {
		$Text = preg_replace("/\[zmg\=([0-9]*)x([0-9]*)\](.*?)\[\/zmg\]/ism", '<img class="zrl" src="$3" style="width: 100%; max-width: $1px;" alt="' . t('Image/photo') . '" />', $Text);
	}

	// [img=widthxheight float={left, right}]pathtoimage[/img]
	if (strpos($Text,'[/img]') !== false) {
		$Text = preg_replace("/\[img\=([0-9]*)x([0-9]*) float=left\](.*?)\[\/img\]/ism", '<img src="$3" style="width: 100%; max-width: $1px; float: left;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/img]') !== false) {
		$Text = preg_replace("/\[img\=([0-9]*)x([0-9]*) float=right\](.*?)\[\/img\]/ism", '<img src="$3" style="width: 100%; max-width: $1px; float: right;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/zmg]') !== false) {
		$Text = preg_replace("/\[zmg\=([0-9]*)x([0-9]*) float=left\](.*?)\[\/zmg\]/ism", '<img class="zrl" src="$3" style="width: 100%; max-width: $1px; float: left;" alt="' . t('Image/photo') . '" />', $Text);
	}
	if (strpos($Text,'[/zmg]') !== false) {
		$Text = preg_replace("/\[zmg\=([0-9]*)x([0-9]*) float=right\](.*?)\[\/zmg\]/ism", '<img class="zrl" src="$3" style="width: 100%; max-width: $1px; float: right;" alt="' . t('Image/photo') . '" />', $Text);
	}

	// style (sanitized)
	if (strpos($Text,'[/style]') !== false) {
		$Text = preg_replace_callback("(\[style=(.*?)\](.*?)\[\/style\])ism", "bb_sanitize_style", $Text);
	}

	// crypt
	if (strpos($Text,'[/crypt]') !== false) {
		$x = random_string();
		$Text = preg_replace("/\[crypt\](.*?)\[\/crypt\]/ism",'<br /><div id="' . $x . '"><img src="' .$a->get_baseurl() . '/images/lock_icon.gif" onclick="red_decrypt(\'rot13\',\'\',\'$1\',\'#' . $x . '\');" alt="' . t('Encrypted content') . '" title="' . t('Encrypted content') . '" /><br /></div>', $Text);
		$Text = preg_replace_callback("/\[crypt (.*?)\](.*?)\[\/crypt\]/ism", 'bb_parse_crypt', $Text);
	}

	if(strpos($Text,'[/app]') !== false) {
		$Text = preg_replace_callback("/\[app\](.*?)\[\/app\]/ism",'bb_parse_app', $Text);
	}

	if(strpos($Text,'[/element]') !== false) {
		$Text = preg_replace_callback("/\[element\](.*?)\[\/element\]/ism",'bb_parse_element', $Text);
	}

	// html5 video and audio
	if (strpos($Text,'[/video]') !== false) {
		$Text = preg_replace_callback("/\[video\](.*?\.(ogg|ogv|oga|ogm|webm|mp4|mpeg|mpg))\[\/video\]/ism", 'tryzrlvideo', $Text);
	}
	if (strpos($Text,'[/audio]') !== false) {
		$Text = preg_replace_callback("/\[audio\](.*?\.(ogg|ogv|oga|ogm|webm|mp4|mp3|opus))\[\/audio\]/ism", 'tryzrlaudio', $Text);
	}
	if (strpos($Text,'[/zvideo]') !== false) {
		$Text = preg_replace_callback("/\[zvideo\](.*?\.(ogg|ogv|oga|ogm|webm|mp4|mpeg|mpg))\[\/zvideo\]/ism", 'tryzrlvideo', $Text);
	}
	if (strpos($Text,'[/zaudio]') !== false) {
		$Text = preg_replace_callback("/\[zaudio\](.*?\.(ogg|ogv|oga|ogm|webm|mp4|mp3|opus))\[\/zaudio\]/ism", 'tryzrlaudio', $Text);
	}

	// Try to Oembed
	if ($tryoembed) {
		if (strpos($Text,'[/video]') !== false) {
			$Text = preg_replace_callback("/\[video\](.*?)\[\/video\]/ism", 'tryoembed', $Text);
		}
		if (strpos($Text,'[/audio]') !== false) {
			$Text = preg_replace_callback("/\[audio\](.*?)\[\/audio\]/ism", 'tryoembed', $Text);
		}

		if (strpos($Text,'[/zvideo]') !== false) {
			$Text = preg_replace_callback("/\[zvideo\](.*?)\[\/zvideo\]/ism", 'tryoembed', $Text);
		}
		if (strpos($Text,'[/zaudio]') !== false) {
			$Text = preg_replace_callback("/\[zaudio\](.*?)\[\/zaudio\]/ism", 'tryoembed', $Text);
		}
	}

	// if video couldn't be embedded, link to it instead.
	if (strpos($Text,'[/video]') !== false) {
		$Text = preg_replace("/\[video\](.*?)\[\/video\]/", '<a href="$1">$1</a>', $Text);
	}
	if (strpos($Text,'[/audio]') !== false) {
		$Text = preg_replace("/\[audio\](.*?)\[\/audio\]/", '<a href="$1">$1</a>', $Text);
	}

	if (strpos($Text,'[/zvideo]') !== false) {
		$Text = preg_replace("/\[zvideo\](.*?)\[\/zvideo\]/", '<a class="zid" href="$1">$1</a>', $Text);
	}
	if (strpos($Text,'[/zaudio]') !== false) {
		$Text = preg_replace("/\[zaudio\](.*?)\[\/zaudio\]/", '<a class="zid" href="$1">$1</a>', $Text);
	}

	if ($tryoembed){
		if (strpos($Text,'[/iframe]') !== false) {
			$Text = preg_replace_callback("/\[iframe\](.*?)\[\/iframe\]/ism", 'bb_iframe', $Text);
		}
	} else {
		if (strpos($Text,'[/iframe]') !== false) {
			$Text = preg_replace("/\[iframe\](.*?)\[\/iframe\]/ism", '<a href="$1">$1</a>', $Text);
		}
	}

	// Youtube extensions
	if (strpos($Text,'[youtube]') !== false) {
		if ($tryoembed) {
			$Text = preg_replace_callback("/\[youtube\](https?:\/\/www.youtube.com\/watch\?v\=.*?)\[\/youtube\]/ism", 'tryoembed', $Text);
			$Text = preg_replace_callback("/\[youtube\](www.youtube.com\/watch\?v\=.*?)\[\/youtube\]/ism", 'tryoembed', $Text);
			$Text = preg_replace_callback("/\[youtube\](https?:\/\/youtu.be\/.*?)\[\/youtube\]/ism", 'tryoembed', $Text);
		}
			$Text = preg_replace("/\[youtube\]https?:\/\/www.youtube.com\/watch\?v\=(.*?)\[\/youtube\]/ism", '[youtube]$1[/youtube]', $Text);
			$Text = preg_replace("/\[youtube\]https?:\/\/www.youtube.com\/embed\/(.*?)\[\/youtube\]/ism", '[youtube]$1[/youtube]', $Text);
			$Text = preg_replace("/\[youtube\]https?:\/\/youtu.be\/(.*?)\[\/youtube\]/ism", '[youtube]$1[/youtube]', $Text);

		if ($tryoembed)
			$Text = preg_replace("/\[youtube\]([A-Za-z0-9\-_=]+)(.*?)\[\/youtube\]/ism", '<iframe width="' . $a->videowidth . '" height="' . $a->videoheight . '" src="http://www.youtube.com/embed/$1" frameborder="0"></iframe>', $Text);
		else
			$Text = preg_replace("/\[youtube\]([A-Za-z0-9\-_=]+)(.*?)\[\/youtube\]/ism", "http://www.youtube.com/watch?v=$1", $Text);
	}
	if (strpos($Text,'[vimeo]') !== false) {
		if ($tryoembed) {
			$Text = preg_replace_callback("/\[vimeo\](https?:\/\/player.vimeo.com\/video\/[0-9]+).*?\[\/vimeo\]/ism", 'tryoembed', $Text);
			$Text = preg_replace_callback("/\[vimeo\](https?:\/\/vimeo.com\/[0-9]+).*?\[\/vimeo\]/ism", 'tryoembed', $Text);
		}

		$Text = preg_replace("/\[vimeo\]https?:\/\/player.vimeo.com\/video\/([0-9]+)(.*?)\[\/vimeo\]/ism", '[vimeo]$1[/vimeo]', $Text);
		$Text = preg_replace("/\[vimeo\]https?:\/\/vimeo.com\/([0-9]+)(.*?)\[\/vimeo\]/ism", '[vimeo]$1[/vimeo]', $Text);

		if ($tryoembed)
			$Text = preg_replace("/\[vimeo\]([0-9]+)(.*?)\[\/vimeo\]/ism", '<iframe width="' . $a->videowidth . '" height="' . $a->videoheight . '" src="http://player.vimeo.com/video/$1" frameborder="0" ></iframe>', $Text);
		else
			$Text = preg_replace("/\[vimeo\]([0-9]+)(.*?)\[\/vimeo\]/ism", "http://vimeo.com/$1", $Text);
	}

	// oembed tag
	$Text = oembed_bbcode2html($Text);

	// Avoid triple linefeeds through oembed
	$Text = str_replace("<br style='clear:left'></span><br /><br />", "<br style='clear:left'></span><br />", $Text);

	// If we found an event earlier, strip out all the event code and replace with a reformatted version.
	// Replace the event-start section with the entire formatted event. The other bbcode is stripped.
	// Summary (e.g. title) is required, earlier revisions only required description (in addition to 
	// start which is always required). Allow desc with a missing summary for compatibility.

	if ((x($ev,'desc') || x($ev,'summary')) && x($ev,'start')) {

		$sub = format_event_html($ev);

		$sub = str_replace('$',"\0",$sub);

		$Text = preg_replace("/\[event\-start\](.*?)\[\/event\-start\]/ism",$sub,$Text); 

		$Text = preg_replace("/\[event\-summary\](.*?)\[\/event\-summary\]/ism",'',$Text);
		$Text = preg_replace("/\[event\-description\](.*?)\[\/event\-description\]/ism",'',$Text);
		$Text = preg_replace("/\[event\-finish\](.*?)\[\/event\-finish\]/ism",'',$Text);
		$Text = preg_replace("/\[event\-location\](.*?)\[\/event\-location\]/ism",'',$Text);
		$Text = preg_replace("/\[event\-adjust\](.*?)\[\/event\-adjust\]/ism",'',$Text);

		$Text = str_replace("\0",'$',$Text);

	}

	// Unhide all [noparse] contained bbtags unspacefying them 
	// and triming the [noparse] tag.
	if (strpos($Text,'[noparse]') !== false) {
		$Text = preg_replace_callback("/\[noparse\](.*?)\[\/noparse\]/ism", 'bb_unspacefy_and_trim', $Text);
	}
	if (strpos($Text,'[nobb]') !== false) {
		$Text = preg_replace_callback("/\[nobb\](.*?)\[\/nobb\]/ism", 'bb_unspacefy_and_trim', $Text);
	}
	if (strpos($Text,'[pre]') !== false) {
		$Text = preg_replace_callback("/\[pre\](.*?)\[\/pre\]/ism", 'bb_unspacefy_and_trim', $Text);
	}

	$Text = preg_replace('/\[\&amp\;([#a-z0-9]+)\;\]/', '&$1;', $Text);

	// fix any escaped ampersands that may have been converted into links
	$Text = preg_replace("/\<(.*?)(src|href)=(.*?)\&amp\;(.*?)\>/ism", '<$1$2=$3&$4>', $Text);

	$Text = preg_replace("/\<(.*?)(src|href)=\"[^zhfm#](.*?)\>/ism", '<$1$2="">', $Text);

	$Text = bb_replace_images($Text, $saved_images);

	$Text = bb_replace_forms($Text, $saved_forms);

	call_hooks('bbcode', $Text);

	return $Text;
}

/**
 * This function exists as a short-term solution to folks linking to private images from their /cloud in
 * their profiles, which brings up a login dialogue in the directory when that entry is viewed.
 * The long term solution is to separate the web file browser from DAV so that you'll never see a 
 * login prompt (though the resource may return a permission denied). 
 */



function strip_bbimage($s) {
		return preg_replace("/\[[zi]mg(.*?)\](.*?)\[\/[zi]mg\]/ism", '', $s);
}
