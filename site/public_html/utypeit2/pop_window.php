<?
session_start();

require_once('src/globals.php');

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
		$title = 'Recipe Formats';
		$navigation = '';
		
		$out = '<table>';
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
		$title = 'Filler Sets';
		
		$navigation = '
	<ul id="top_nav">
		<li><a href="'.$_SERVER['PHP_SELF'].'?action=filler_sets&type=text" target="_self">Show Text Fillers</a></li>
		<li><a href="'.$_SERVER['PHP_SELF'].'?action=filler_sets&type=image" target="_self">Show Image Fillers</a></li>
	</ul>';
		
		$out = "<table>\r";
		if($type == 'text') {
			foreach($this_set['set'] AS $f) {
				$thename = (string) $f['@attributes']['name'];
				$out .= "<tr><td colspan='2' class='thename b_pms659'>".$thename."</td></tr>";
				if($f['filler']) {
				    foreach($f['filler'] AS $e) {
				        $out .= "<tr><td colspan='2' class=\"item left b_white\">".$e."</td></tr>";
				    }
                }
                $out .= '
                </table>
                <table>';
			}
			$out .= '</table>';
		} else {
			$mod = 2;
			for($i=0;$i<count($this_set['set']);$i++) {
				$thename = (string) $this_set['set'][$i]['@attributes']['name'];
				if($i%$mod == 0) {
					$out .= "</tr><tr>";
				}
				$out .= '<td syle="width: 200px">';
				$out .= "<div class='thename b_pms659'>".$thename."</div>";
				$theimage = $this_set['set'][$i]['filler']['image'];
				$thethumb = $this_set['set'][$i]['filler']['thumbnail'];
				$out .= "\t\t<div class='item b_white'>";
				$out .= "\t\t<a class='lightwindow' rel=\"Fillers[Filler Sets]\" href='".IMAGES."filler_images/".$theimage."' title=\"".$thename."\"><img src='".IMAGES."filler_images/".$thethumb."' border='0'></a>\r";
				$out .= "\t\t</div>";
				$out .= '</td>';
			}
		}
		$out .= "</table>\r";
		break;
	case 'user_levels':
		$title = 'User Levels';
		$out = '
			<h3>Chairperson</strong></h3>
			The chairperson is the individual responsible for oversight of all cookbook order functions. The chairperson is solely responsible for the submission of orders for production.</p>
			<h3>Cochariperson</h3>
			The cochairperson is the individual on record as the assistant to the chairperson, and is the cosigner for the order. The cochairperson is only superseded by the chairperson.</p>
			<h3>Committee Member</h3>
			Committee members can change, or edit recipes entered by contributors. They cannot change order settings, but they can add other committee members, or contributors.</p>
			<h3>Contributor</h3>
			Contributors can add recipes and edit their own recipes.</p>
		';
		break;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=$action?></title>
	<link href="media/css/reset.css" rel="stylesheet" type="text/css" />
	<link href="../webfonts/css/webfonts.css" rel="stylesheet" type="text/css" />
	<link href="media/css/colors.css" rel="stylesheet" type="text/css" />
	<link href="media/css/pop_style.css" rel="stylesheet" type="text/css" />
	<link href="media/css/lightwindow.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?=U_JS?>prototype.js"></script>
	<script type="text/javascript" src="<?=U_JS?>scriptaculous.js"></script>
	<script type="text/javascript" src="<?=U_JS?>lightwindow.js"></script>
</head>

<body>
	<div class="title b_ds300-3 t_white"><h1><?=$title?></h1></div>
	<div class="navigation b_ds300-3"><?=$navigation?></div>
<?=$out?>
</body>
</html>