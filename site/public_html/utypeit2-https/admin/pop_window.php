<?
session_start();

require_once('../src/globals.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CPI OMS Admin</title>
<link href="<?=A_CSS?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>pop_style.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>lightwindow.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=A_JS?>prototype.js"></script>
<script type="text/javascript" src="<?=A_JS?>scriptaculous.js"></script>
<script type="text/javascript" src="<?=A_JS?>lightwindow.js"></script>
</head>

<body>

<?php

if($_GET['action']) {
	$action = $_GET['action'];
}
if($_GET['type']) {
	$type = $_GET['type'];
}

function getPageData($page) {
	$fileref = DATA.'xml/'.$page.".xml";
	$data = simplexml_load_file($fileref);
	return($data);
}

switch($action) {
	case 'recipe_formats':
		$data = getPageData('recipe_formats');
		$data_set = json_encode($data);
		$array = json_decode($data_set,TRUE);
		//print_r($array);
		// show a grid of format thumbnails, link them to the bigger image using light box
		$out = "<table width='100%' border='0' cellpadding='0' cellspacing='4'>\r";
		$out .= "\t<tr><td colspan='4'  class='popHeader'>Recipe Formats</td>\r";
		$i = 0;
		$this_set = $array['format'];
		foreach($this_set AS $f) {
			if($i%2 == 0) {
				$out .= "\t</tr>\r";
				$out .= "\t<tr>\r";
			}
			$out .= "\t\t<td class='item'>";
			$thename = (string) $f['name'];
			$thedescription = (string) $f['description'];
			$out .= "<a class='lightwindow' rel=\"Formats[Formats]\" href='".IMAGES.$f['image1']."' title='".$thedescription."'>";
			$out .= "<img src=\"".IMAGES.$f['thumbnail1']."\" border=\"0\" hspace=\"2\"></a>";
			$out .= "<a class='lightwindow' rel=\"Formats[Formats]\" href='".IMAGES.$f['image2']."' title='".$thedescription."'>";
			$out .= "<img src='".IMAGES.$f['thumbnail2']."' border='0' hspace='2'></a>";
			$out .= "<p class='name'>".$thename."</p></a></td>\r";
			$i++;
		}
		$out .= "\t</tr>\r";
		$out .= "</table>\r";
		break;
	case 'filler_sets':
		$data = getPageData('filler_sets');
		// if the fillers are text, list the filler text, by set.
		// if the fillers are image, show a grid of format thumbnails, link them to the bigger image using light box
		$data_set = json_encode($data);
		$array = json_decode($data_set,TRUE);
		
		switch($type) {
			case 'text':
				$this_set = $array['text_fillers'];
				$mod = 0;
				break;
			case 'image':
				$this_set = $array['image_fillers'];
				$mod = 2;
				break;
			default:
				$this_set = $array['image_fillers'];
				$mod = 2;
		}
		
		$out = "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\r";
		$out .= "\t<tr><td class='popHeader' colspan='2'>Filler Sets</td></tr>\r";
		$out .= "\t<tr><td class='popTopLeft'><a href='".$_SERVER['PHP_SELF']."?action=filler_sets&type=text' target='_self'>Show Text Fillers</a></td><td  class='popTopRight'><a href='".$_SERVER['PHP_SELF']."?action=filler_sets&type=image' target='_self'>Show Image Fillers</a></td></tr>\r";
		
		
		if($type == 'text') {
			foreach($this_set['set'] AS $f) {
				$thename = (string) $f['@attributes']['name'];
				$out .= "<tr><td colspan='2' class='thename'>".$thename."</td></tr>";
				if($f['filler']) {
				    foreach($f['filler'] AS $e) {
				        $out .= "<tr><td  colspan='2' style='background-color: #EFEFEF'>".$e."</td></tr>";
				    }
                }
			}
		} else {
			$mod = 2;
			for($i=0;$i<count($this_set['set']);$i++) {
				$thename = (string) $this_set['set'][$i]['@attributes']['name'];
				if($i%$mod == 0) {
					$out .= "</tr><tr>";
				}
				$out .= '<td syle="width: 200px">';
				$out .= "<div class='thename'>".$thename."</div>";
				$theimage = $this_set['set'][$i]['filler']['image'];
				$thethumb = $this_set['set'][$i]['filler']['thumbnail'];
				$out .= "\t\t<div class='item'>";
				$out .= "\t\t<a class='lightwindow' rel=\"Fillers[Filler Sets]\" href='".IMAGES."filler_images/".$theimage."' title=\"".$thename."\"><img src='".IMAGES."filler_images/".$thethumb."' border='0'></a>\r";
				$out .= "\t\t</div>";
				$out .= '</td>';
			}
		}
		$out .= "</table>\r";
		break;
}

echo $out;
?>
</body>
</html>