<?
class BreadCrumb
{
	var $page = '';
	
	function __construct($page){
		$this->page = $page;
	}
	
	public function _paginate($current,$total,$limit,$class,$orderby,$action='') {
		$out = '';
		if($total > $limit) {
			// The total number of pages possible
			$page_count = ceil($total / $limit);
			
			//only display page numbers: n limit numbers above, and n limit numbers below the current page
			//(not going before 0 or past the max number)
			$pagetotal = 5; // maximum page numbers to display at once, must be an odd number
			$pagelimit = ($pagetotal-1)/2;
			$pagemax = $pagetotal>$page_count?$page_count:$pagetotal;
			
			if ($current - $pagelimit < 1) {
				$pagemin = 1;
			}
			
			if ($current - $pagelimit >=1 && $current + $pagelimit <= $page_count) {
				$pagemin = $current - $pagelimit;
				$pagemax = $current + $pagelimit;
			}
			
			if ($current - $pagelimit >=1 && $current + $pagelimit > $page_count) {
				$pagemin = ($page_count-$pagetotal+1)<1?1:($page_count-$pagetotal+1);
				$pagemax = $page_count;
			}
			
			if ($current + $pagelimit > $page_count) {
				$pagemax = $page_count;
			} 
			
			for($p=$pagemin; $p<=$pagemax; $p++)
			{
				
				if($action) {
					$actstring = "&action=".$action;
				} else {
					$actstring = '';
				}
                
				if ($current > 1) {
					$this_page  = $current - 1;
					$list_start = ($limit * ($this_page-1));
					
					$getstring = "start=".$list_start."&limit=".$limit."&orderby=".$orderby."&page=".$this_page.$actstring;
					$prev  = ' <a href="'.$this->page.'.php?'.$getstring.'" class="'.$class.'"><</a> ';
					
					$getstring = "start=0&limit=".$limit."&orderby=".$orderby."&page=1".$actstring;
					$first = ' <a href="'.$this->page.'.php?'.$getstring.'" class="'.$class.'">«</a> ';
				} else {
					$prev  = '&nbsp;'; // we're on page one, don't print previous link
					$first = '&nbsp;'; // nor the first page link
				}
					
				if ($current < $page_count) {
					$this_page = ($current+1);
					$list_start = ($limit * ($current));
					
					$getstring = "start=".$list_start."&limit=".$limit."&orderby=".$orderby."&page=".$this_page.$actstring;
					$next = ' <a href="'.$this->page.'.php?'.$getstring.'" class="'.$class.'">></a> ';
					
					$getstring = "start=".($total-$limit)."&limit=".$limit."&orderby=".$orderby."&page=".$page_count.$actstring;
					$last = ' <a href="'.$this->page.'.php?'.$getstring.'" class="'.$class.'">»</a> ';
				} else {
					$next = '&nbsp;'; // we're on the last page, don't print next link
					$last = '&nbsp;'; // nor the last page link
				}
				
				if ($p == $current) {
					$nav .= '<span style="font-weight: normal; margin: 0 5px 0 5px">'.$p.'</span>'; // no need to create a link to current page
				} else {
					$list_start = ($limit * ($p-1));
					
					$getstring = "start=".$list_start."&limit=".$limit."&orderby=".$orderby."&page=".$p.$actstring;
					$nav .= '<a href="'.$this->page.'.php?'.$getstring.'" class="'.$class.'">'.floor($p).'</a> ';
				}
			}
           
			$out = $first . $prev . $nav . $next . $last;
		}
		return($out);
	}
}
?>