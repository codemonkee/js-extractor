<?php
/*
 * Copyright (c) 2010, Jack Sleight <http://jacksleight.com/>
 * All rights reserved.
 *
 * This source file is subject to the new BSD license that is bundled with this package in the file LICENSE.txt.
 * It is also available at this URL: http://www.opensource.org/licenses/bsd-license.php
 */

class JS_DOM_Document extends DOMDocument
{
	protected $_xPath;

	public function __construct($version = null, $encoding = null)
	{
		parent::__construct($version, $encoding);
		$this->registerNodeClass('DOMDocument', 'JS_DOM_Document');
		$this->registerNodeClass('DOMElement', 'JS_DOM_Element');
	}

	public function createElement($name, $attributes = null, $children = null)
	{
		$element = parent::createElement($name);

		if (!is_array($attributes)) {
			$attributes = isset($attributes) ? array($attributes) : array();
		}
		if (!is_array($children)) {
			$children = isset($children) ? array($children) : array();
		}
		if (!js_is_array_assoc($attributes)) {
			$children = $attributes;
			$attributes = array();
		}
		foreach ($attributes as $name => $value) {
			$element->setAttribute($name, $value);
		}
		foreach ($children as $child) {
			if (!$child instanceof DOMElement) {
				$child = $this->createTextNode((string) $child);
			}
			$element->appendChild($child);
		}

		return $element;
	}

	public function getXPath()
	{
		if (!isset($this->_xPath)) {
			$this->_xPath = new DOMXPath($this);
		}
		return $this->_xPath;
	}

	public function query($expression)
	{
		return $this->getXPath()->query($expression);
	}
}
