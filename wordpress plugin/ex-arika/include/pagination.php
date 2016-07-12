<?php
/*
 ###################################
         Another GET Paginator
               by DSR!
               v1.2.1
 ###################################

[*]BasedOn
http://www.mis-algoritmos.com/2007/05/27/digg-style-pagination-class/ 
Version: 0.41 + 2009-08-09 00:14:09 + 2010-06-17 12:35:42

[*]ListOfFunctions
pagination($total, $limit, $page)
baseURL($value)
adjacents($value)
showCounter($value="")
changeClass($value="")
nextLabel($value)
prevLabel($value)
parameterName($value="")
urlFriendly($value="%")
get_pagenum_link($id)
getOutput()
*/


class pagination{
	/* Default values */
	var $target        = ""; #URL base
	var $parameterName = "p"; #Nombre del parametro que voy a pasar por get
	var $adjacents     = 1; #Ver paginas del final
	var $showCounter   = false;
	var $urlFriendly   = false;
	
	/* Apariencia */
	var $className     = 'scott';//'megas512';//"yellow"; #class html usada por el paginador
	var $nextLabel     = "&#187;Siguiente";//&#9658;
	var $prevLabel     = "Anterior&#171;";//&#9668;
	var $labelSep      = "...";
	

	function pagination($total, $limit, $page){
		$this->total_pages = (int) $total;
		$this->limit       = (int) $limit;
		$this->page        = (int) $page;
	}

	function adjacents($value){
		$this->adjacents = $value;
	}
	
	function showCounter($value=""){
		$this->showCounter = ($value===true)?true:false;
	}

	function changeClass($value=""){
		$this->className = $value;
	}
	
	function nextLabel($value){
		$this->nextLabel = $value;
	}
	
	function prevLabel($value){
		$this->prevLabel = $value;
	}
	
	function parameterName($value=""){
		$this->parameterName = $value;
	}
	
	function baseURL($value){
		//$this->target = $value;
		
		//filtro si en el parametro base esta el parametro del paginador		
		$tmp = strpos($value, "&$this->parameterName=");
		if ($tmp !== false){
			$this->target = substr($value, 0, $tmp);
		} else {
			$tmp = strpos($value, "?$this->parameterName=");
			if ($tmp !== false){
				$this->target = substr($value, 0, $tmp);
			} else {
				$this->target = $value;
			}
		}
	}
	
	function urlFriendly($value="%"){
		if(eregi('^ *$',$value)){
			$this->urlFriendly=false;
			return false;
		}
		$this->urlFriendly=$value;
	}
		
	function get_pagenum_link($id){
		if(strpos($this->target,'?')===false)
				if($this->urlFriendly)
						return str_replace($this->urlFriendly,$id,$this->target);
					else
						return "$this->target?$this->parameterName=$id";
			else
				return "$this->target&$this->parameterName=$id";
	}
		
	function getOutput(){
		$pagination = "";
		$error = false;

		if($this->urlFriendly and $this->urlFriendly != '%' and strpos($this->target,$this->urlFriendly)===false){
			//Es necesario especificar el comodin para sustituir
			echo "Especificaste un wildcard para sustituir, pero no existe en el target<br />";
			$error = true;
		} elseif($this->urlFriendly and $this->urlFriendly == '%' and strpos($this->target,$this->urlFriendly)===false) {
			echo "Es necesario especificar en el target el comodin % para sustituir el número de página<br />";
				$error = true;
		}

		if ($this->total_pages < 0) {
			echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
			$error = true;
		}
		
		if ($this->limit == null) {
			echo "It is necessary to specify the <strong>limit of items</strong> to show per page (\$class->limit(10))<br />";
			$error = true;
		}

		if ($error) {
			return false;
		}
			
		// fix 2010-06-17 12:35:42
		$this->page = $this->page ? $this->page : 1;
		/* Setup vars for query. */
		/*
		if ($this->page) {
			//first item to display on this page
			$start = ($this->page - 1) * $this->limit;
		} else {
			//if no page var is given, set start to 0
			$start = 0;
		}
		*/
		
		/* Setup page vars for display. */
		$prev = $this->page - 1;//previous page is page - 1
		$next = $this->page + 1;//next page is page + 1
		$lastpage = ceil($this->total_pages/$this->limit);//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;//last page minus 1
		
		/* 
			Now we apply our rules and draw the pagination object. 
			We're actually saving the code to a variable in case we want to draw it more than once.
		*/
			
		if($lastpage > 1){
			if($this->page){
				//anterior button
				if($this->page > 1)
					$pagination .= "<a href=\"".$this->get_pagenum_link($prev)."\" class=\"prev\">".$this->prevLabel."</a>";
				else
					$pagination .= "<span class=\"disabled\">".$this->prevLabel."</span>";
			}

			//pages	
			if ($lastpage < 7 + ($this->adjacents * 2)){//not enough pages to bother breaking it up
				for ($counter = 1; $counter <= $lastpage; $counter++){
					if ($counter == $this->page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
				}
			//enough pages to hide some
			} elseif ($lastpage > 5 + ($this->adjacents * 2)) {
				//close to beginning; only hide later pages
				// fix 2009-08-09 00:14:09
				//if($this->page < 1 + ($this->adjacents * 2)){
				if ($this->page < 1 + ($this->adjacents * 2) || ($this->adjacents == 1 && $this->page == 3)) {
					for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++){
						if ($counter == $this->page)
							$pagination .= "<span class=\"current\">$counter</span>";
						else
							$pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
					}
					
					$pagination .= $this->labelSep;
					$pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
					$pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
				
				//in middle; hide some front and some back
				} elseif ($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)) {
					$pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
					$pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
						$pagination .= $this->labelSep;
						for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
							if ($counter == $this->page)
								$pagination .= "<span class=\"current\">$counter</span>";
							else
								$pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
								$pagination .= $this->labelSep;
								$pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
								$pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
				
				//close to end; only hide early pages
				} else {
						$pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
						$pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
						$pagination .= $this->labelSep;
						for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
							if ($counter == $this->page)
								$pagination .= "<span class=\"current\">$counter</span>";
							else
								$pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
				}
			}
				
			if($this->page){
				//siguiente button
				if ($this->page < $counter - 1)
					$pagination .= "<a href=\"".$this->get_pagenum_link($next)."\" class=\"next\">".$this->nextLabel."</a>";
				else
					$pagination .= "<span class=\"disabled\">".$this->nextLabel."</span>";
				if($this->showCounter)$pagination .= "<div class=\"pagination_data\">($this->total_pages Pages)</div>";
			}
		}

			return "<div class=\"$this->className\">".$pagination."</div>\n";
	}
}
?>