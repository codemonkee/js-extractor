<?php
/*
 * Copyright (c) 2010, Jack Sleight <http://jacksleight.com/>
 * All rights reserved.
 *
 * This source file is subject to the new BSD license that is bundled with this package in the file LICENSE.txt.
 * It is also available at this URL: http://www.opensource.org/licenses/bsd-license.php
 */

class JS_Extractor_Element extends JS_DOM_Element
{
	public function __call($method, $args)
	{
		if (method_exists($this, $name = '_' . $this->tagName . '_' . $method)) {
			return call_user_func_array(array($this, $name), $args);
		}
		throw new JS_Extractor_Exception("Call to unknown method '$method' for element type '$this->tagName'");
	}
	
	protected function _table_splitCells()
	{
		$this->_splitCells();
		foreach ($this->query('thead') as $thead) {
			$thead->splitCells();
		}
		foreach ($this->query('tfoot') as $tfoot) {
			$tfoot->splitCells();
		}
		foreach ($this->query('tbody') as $tbody) {
			$tbody->splitCells();
		}
		return $this;
	}

	protected function _thead_splitCells()
	{
		$this->_splitCells();
		return $this;
	}
	
	protected function _tfoot_splitCells()
	{
		$this->_splitCells();
		return $this;
	}
	
	protected function _tbody_splitCells()
	{
		$this->_splitCells();
		return $this;
	}
	
	protected function _splitCells()
	{
		foreach ($trs = $this->query('tr') as $r => $tr) {
			foreach ($tr->query('th|td') as $c => $td) {
				if ($td->hasAttribute('rowspan') && is_numeric($rowspan = $td->getAttribute('rowspan'))) {
					$td->removeAttribute('rowspan');
					for ($s = 1; $s < $rowspan; $s++) {
						if ($next = $trs->item($r + $s)) {
							$next->insertBefore($td->cloneNode(true), $next->query('th|td')->item($c));
						}
					}
				}
				if ($td->hasAttribute('colspan') && is_numeric($colspan = $td->getAttribute('colspan'))) {
					$td->removeAttribute('colspan');
					for ($s = 1; $s < $colspan; $s++) {
						$tr->insertAfter($td->cloneNode(true), $td);
					}
				}
			}
		}
	}
	
	public function extract($expressions, $type = JS_Extractor::EXTRACT_TEXT, $attribute = null)
	{
		$data = array();
		$expressions = (array) $expressions;
		$i = key($expressions);
		$parts = (array) array_shift($expressions);
		foreach ($parts as $j => $part) {
			if (!is_int($j)) {
				$name = $j;
			} elseif (!is_int($i)) {
				$name = $i;
			} else {
				$name = null;
			}
			foreach ($this->query($part) as $element) {
				if (isset($name) && !isset($data[$name])) {
					$data[$name] = array();
				}
				if (isset($name)) {
					$pointer =& $data[$name];
				} else {
					$pointer =& $data;
				}
				if (empty($expressions)) {
					switch ($type) {
						case JS_Extractor::EXTRACT_TEXT:
							$pointer[] = $element->textContent;
						break;
						case JS_Extractor::EXTRACT_ATTRIBUTE:
							$pointer[] = $element->getAttribute($attribute);
						break;
						case JS_Extractor::EXTRACT_ELEMENT:
							$pointer[] = $element;
						break;
					}
				} else {
					$pointer[] = $element->extract($expressions, $type, $attribute);
				}
			}
		}
		return $data;
	}
}