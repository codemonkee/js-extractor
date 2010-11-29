<?php
/* 
 * Copyright 2008-2010 Jack Sleight <http://jacksleight.com/>
 * All rights reserved.
 * 
 * Any redistribution or reproduction of part or all of the contents in any form is prohibited.
 */

class JS_DOM_Element extends DOMElement
{
	public function setAttribute($name, $value)
	{
		if (is_array($value)) {
			$value = implode(' ', $value);
		}
		return parent::setAttribute($name, $value);
	}
		
	public function query($expression)
	{
		return $this->ownerDocument->getXPath()->query($expression, $this);
	}

	public function insertAfter($newnode, $refnode = null)
	{
		if (!isset($refnode) || $refnode === $this->parentNode->lastChild) {
			$this->parentNode->appendChild($newnode);
		} else {
			$this->insertBefore($newnode, $refnode->nextSibling);
		}
	}
	
	public function getOuterHTML()
	{
		$document = new JS_DOM_Document();
		$document->appendChild($document->importNode($this, true));
		return trim($document->saveHTML());
	}

	public function getInnerHTML()
	{
		$document = new JS_DOM_Document();
		foreach ($this->childNodes as $child) {
			$document->appendChild($document->importNode($child, true));
		}
		return $document->saveHTML();
	}

	public function removeChildren()
	{
		while ($this->firstChild) {
			$this->removeChild($this->firstChild);
		}
		return $this;
	}

	public function setInnerHTML($content)
	{
		$this->removeChildren();

		$document = new JS_DOM_Document();
		@$document->loadHTML('
			<!DOCTYPE html>
			<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=' . $this->ownerDocument->actualEncoding . '">
				<title></title>
			</head>
			<body>
				' . $content . '
			</body>
			</html>
		');

		$body = $document->getElementsByTagName('body')->item(0);
		foreach ($body->childNodes as $child) {
			$child = $this->ownerDocument->importNode($child, true);
			$this->appendChild($child);
		}

		return $this;
	}
}