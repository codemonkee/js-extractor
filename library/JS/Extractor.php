<?php
class JS_Extractor extends JS_DOM_Document
{
	const EXTRACT_TEXT		= 'EXTRACT_TEXT';
	const EXTRACT_ATTRIBUTE	= 'EXTRACT_ATTRIBUTE';
	const EXTRACT_ELEMENT	= 'EXTRACT_ELEMENT';

	public function __construct($data, $version = null, $encoding = null)
	{
		parent::__construct($version, $encoding);
		$this->registerNodeClass('DOMDocument', 'JS_Extractor');
		$this->registerNodeClass('DOMElement', 'JS_Extractor_Element');
		@$this->loadHTML($data);
	}
}