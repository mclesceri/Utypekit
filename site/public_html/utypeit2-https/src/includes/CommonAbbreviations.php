<?php

class CommonAbbreviations
{
    
    function _draw() {
        $out = "
            <div id=\"common_abbreviations\" class=\"sideBox shown\">
                <div id=\"ca_tab\" class=\"sideTab\">Common Abbreviations</div>
                <div id=\"ca_content\" class=\"sideContent\">
                    <p>Need Help <a href=\"".HELP."common_abbreviations.html\" title=\"Common Abbreviations\" class=\"lightwindow help\" params=\"lightwindow_width=500,lightwindow_height=300\">?</a></p>";
                $out .= '<table id="abbr">';
        $options = array('Tbsp.'=>'tablespoon(s)',
                                         'tsp.'=>'teaspoon(s)',
                                         'c.'=>'cup(s)',
                                         'oz.'=>'ounce(s)',
                                         'lb.'=>'pound(s)',
                                         'pkg.'=>'package(s)',
                                         'qt.'=>'quart(s)',
                                         'pt.'=>'pint(s)',
                                         'gal.'=>'gallon(s)',
                                         'doz.'=>'dozen',
                                         'env.'=>'envelope(s)',
                                         'fl.'=>'fluid (fluid ounce)',
                                         'sq.'=>'square(s)',
                                         
                                         'approx.'=>'approximately',
                                         'bu.'=>'bushel(s)',
                                         'ctn.'=>'carton(s)',);
        $out .= "<tr>";
        $i = 0;
        foreach($options AS $key=>$val) {
            if($i%1 == 0) {
                $out .= "</tr><tr>";
            }
            $out .= "<td class=\"key\">".$key."</td>";
            $out .= "<td class=\"val\">".$val."</td>";
            $i++;
        }
        $out .= "</tr>
        </table>
        ";
                
                
                $out .= "</div>
            </div>";
        return($out);
        
    }

}
?>