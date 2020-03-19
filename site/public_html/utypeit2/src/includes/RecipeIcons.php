<?php

class RecipeIcons
{
    
    function _draw(){
        $out = "<div id=\"recipe_icons\" class=\"sideBox shown\">
                <div id=\"ri_tab\" class=\"sideTab\">Recipe Icons</div>
                <div id=\"ri_content\" class=\"sideContent\">
                <p>Need Help  <a href=\"".HELP."recipe_icons.html\" title=\"Recipe Icons\" class=\"lightwindow help\" params=\"lightwindow_width=500,lightwindow_height=300\">?</a></p>";
        $res = file_get_contents(DATA . 'xml/recipe_icons.xml');
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $json = json_decode($json);
        foreach ($json->icon AS $icn) {
            $src = IMAGES . $icn -> image;
            $out .= '<div id="' . $icn -> name . '" data-ot="'.$icn->name.'" data-ot-delay="1"><img src="' . $src . '" alt="' . $icn -> name . '" onclick="_setRecipeIcon(this)" /></div>';
        }
        $out .= "
                </div>
            </div>";
        return($out);
    }
    
}

?>