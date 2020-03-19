<?

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'Orders.php');

class Order
{
		public function _orderparts($part,$attr) {
			switch($part) {
				case 'person':
					return($this->_person($attr));
					break;
			}
		}
		
		protected function _person($attr) {
			$no = new Orders();
			parse_str($attr,$vars);
			$out = '<div class="personPart" id="person_'.$vars['count'].'"><input type="hidden" name="person_'.$vars['count'].'" value="'.$vars['id'].'">
							<label>'.$vars['name'].'&nbsp;
							<select name="personlevel_'.$vars['count'].'" style="vertical-align: middle">';
			$options = array('0'=>' -- ','1'=>'Demo','2'=>'Contributor','3'=>'Committee','4'=>'Cochairperson','5'=>'Chairperson');
			foreach($options as $k=>$o) {
				$selected = '';
				if($vars['level'] == $k) {
					$selected = ' selected="selected"';
				}
				$out .= 	'<option value="'.$k.'"'.$selected.'>'.$o.'</option>';
			}
			$out .= 		'</select>
							</label>
							<img id="sectionRemoveButton" src="'.IMAGES.'remove_button.png" onclick="setPersonListItem(\''.$vars['id'].'\',\'remove\',\''.$vars['count'].'\')" align="middle">
						</div>';
			return($out);
		}
}

?>