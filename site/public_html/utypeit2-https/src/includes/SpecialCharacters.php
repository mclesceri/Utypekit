<?php

class SpecialCharacters
{
    var $specChars = array(
			'176',
			'8226',
			'8260',
			'169',
			'174',
			'8482',
			'8212',
			'224',// 	à 	latin small letter a with grave
			'225',//	á 	latin small letter a with acute
			'226',//	â 	latin small letter a with circumflex
			'227',//	ã 	latin small letter a with tilde
			'228',//	ä 	latin small letter a with diaeresis
			'229',//	å 	latin small letter a with ring above
			'230',//	æ 	latin small letter ae
			'232',//	è 	latin small letter e with grave
			'233',//	é 	latin small letter e with acute
			'234',//	ê 	latin small letter e with circumflex
			'235',//	ë 	latin small letter e with diaeresis
			'236',//	ì 	latin small letter i with grave
			'237',//	í 	latin small letter i with acute
			'238',//	î 	latin small letter i with circumflex
			'239',//	ï 	latin small letter i with diaeresis
			'209',//	Ñ 	latin capital letter N with tilde
			'241',//	ñ 	latin small letter n with tilde
			'240',//	ð 	latin small letter eth
			'241',//	ñ 	latin small letter n with tilde
			'242',//	ò 	latin small letter o with grave
			'243',//	ó 	latin small letter o with acute
			'244',//	ô 	latin small letter o with circumflex
			'245',//	õ 	latin small letter o with tilde
			'246',//	ö 	latin small letter o with diaeresis);
			'249',//	ù 	latin small letter u with grave
			'250',//	ú 	latin small letter u with acute
			'251',//	û 	latin small letter u with circumflex
			'252',//	ü 	latin small letter u with diaeresis
			'192',//	À 	latin capital letter A with grave
			'193',//	Á 	latin capital letter A with acute
			'194',//	Â 	latin capital letter A with circumflex
			'200',//	È 	latin capital letter E with grave
			'201',//	É 	latin capital letter E with acute
			'217',//	Ù 	latin capital letter U with grave
			'218',//	Ú 	latin capital letter U with acute
			'219',//	Û 	latin capital letter U with circumflex
			'220',//	Ü 	latin capital letter U with diaeresis
			'382',//	ž	latin small letter z with caron
			'381',//	Ž	latin capital letter z with caron
			'8220',//	“ 	left double quotation mark
			'8221',//	” 	right double quotation mark
			'8216',//	‘ 	left single quotation mark
			'8217',//	’ 	right single quotation mark
			'780',//		caron
			);
    function _draw() {
        $out = "
            <div id=\"special_characters\" class=\"sideBox shown\">
                <div id=\"sc_tab\" class=\"sideTab\">Special Characters</div>
                <div id=\"sc_content\" class=\"sideContent\">
                <p>Need Help <a href=\"".HELP."special_characters.html\" title=\"Special Characters\" class=\"lightwindow help\" params=\"lightwindow_width=500,lightwindow_height=300\">?</a></p>";
        
        foreach ($this->specChars AS $s) {
        	$char = '&#'.$s.';';
            $out .= '<div id="' . $s . '"><a href="#" onclick="fillSpecial(\'&#' . $s . ';\'); return false;">' . $char . '</a></div>';
        }
        $out .= "
                </div>
            </div>";
         return($out);
    }

}
?>