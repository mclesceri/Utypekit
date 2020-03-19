<?
class ReportCrumb
{
	var $page = '';
	
	public function _paginate($attr=null) {
		/*
		 * $attr is a string that receives:
		 * total, start, limit, orderby, lpage, action, select
		 */
		if($attr) {
			parse_str($attr,$atArr);
			foreach($atArr AS $key=>$val) {
				$$key = $val;
			}
		}
		
		if($total > $limit) {
			$page_count = ceil($total / $limit);	
			//only display page numbers: n limit numbers above, and n limit numbers below the lpage page
			//(not going before 0 or past the max number)
			$pagetotal = 5; // maximum page numbers to display at once, must be an odd number
			$pagelimit = ($pagetotal-1)/2;
			$pagemax = $pagetotal>$page_count?$page_count:$pagetotal;
			
			if ($lpage - $pagelimit < 0) {
				$pagemin = 0;
			}
			
			if ($lpage - $pagelimit >=0 && $lpage + $pagelimit <= $page_count) {
				$pagemin = $lpage - $pagelimit;
				$pagemax = $lpage + $pagelimit;
			}
			
			if ($lpage - $pagelimit >=0 && $lpage + $pagelimit > $page_count) {
				$pagemin = ($page_count-$pagetotal+1)<1?1:($page_count-$pagetotal+1);
				$pagemax = $page_count;
			}
			
			if ($lpage + $pagelimit > $page_count) {
				$pagemax = $page_count;
			} 
			
			for($p=$pagemin; $p<=$pagemax; $p++)
			{
				$list_start = ($limit * $p);
				if ($lpage > 0) {
					$this_page  = $lpage - 1;
					
					$prev  = ' <a href="#" onclick="sendFilter($(\'reportFilter\'),\'html\',\''.$this_page.'\')">&lt;</a> ';
					$first = ' <a href="#" onclick="sendFilter($(\'reportFilter\'),\'html\',\'0\')">&laquo;</a> ';
				} else {
					$prev  = '&nbsp;'; // we're on page one, don't print previous link
					$first = '&nbsp;'; // nor the first page link
				}
					
				if ($lpage < $page_count) {
					$this_page = ($lpage+1);
					
					$next = ' <a href="#" onclick="sendFilter($(\'reportFilter\'),\'html\',\''.$this_page.'\')">&gt;</a> ';
					$last = ' <a href="#" onclick="sendFilter($(\'reportFilter\'),\'html\',\''.$pagemax.'\')">&raquo;</a> ';
				} else {
					$next = '&nbsp;'; // we're on the last page, don't print next link
					$last = '&nbsp;'; // nor the last page link
				}
				if ($p == $lpage) {
					$nav .= ' <span style="font-weight: bold">'.($p + 1).'</span> '; // no need to create a link to current page
				} else {
					$list_limit = $limit;
					$list_start = ($list_limit * $p);
					
					$nav .= '<a href="#" onclick="sendFilter($(\'reportFilter\'),\'html\',\''.$p.'\')">'.(floor($p) + 1).'</a> ';
				}
			}
			$out .= '<div>'.$first . $prev . $nav . $next . $last.'</div>';
		}
		//echo htmlspecialchars($out);
		return($out);
	}
}
?>