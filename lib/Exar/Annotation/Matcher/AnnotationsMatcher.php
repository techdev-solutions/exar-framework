<?php
namespace Exar\Annotation\Matcher;

class AnnotationsMatcher extends SequentialMatcher {
	const REGEX_ANNOTATION_NAME = 						'[a-zA-Z]([a-zA-Z0-9-_\\\\]*[a-zA-Z0-9])*';
	const REGEX_PARAMETER_NAME = 						'[a-zA-Z_][a-zA-Z0-9-_]*';
	const REGEX_PARAMETER_SINGLE_QUOTED_VALUE = 		"'[^']*'";
	const REGEX_PARAMETER_DOUBLE_QUOTED_VALUE = 		'"[^"]*"';
	const REGEX_PARAMETER_NUMBER_VALUE = 				'-?[0-9]*\.?[0-9]*';

	public function __construct() {
		$this->add(new RegexMatcher(self::REGEX_ANNOTATION_NAME)); // annotation name at the beginning
		
		$parametersMatcher = new ParallelMatcher();
		
		$valuesMatcher = new SequentialMatcher();
		$valuesMatcher->add(new StringMatcher('('));
		
		$pMatcher = new ParallelMatcher();
		$pMatcher->add(new SeparatedValuesMatcher(new NameValuePairMatcher())); // for separated parameters
		$pMatcher->add(new SingleValueMatcher()); // for top level parameter
		$pMatcher->add(new StringMatcher()); // for empty parameter
		$valuesMatcher->add($pMatcher);
		
		$valuesMatcher->add(new StringMatcher(')'));

		$parametersMatcher->add($valuesMatcher);
		$parametersMatcher->add(new StringMatcher()); // for annotations without parameters
		
		$this->add($parametersMatcher);
	}
	
	public function match(&$str) {
		$strBackup = $str;
		$result = parent::match($str);
		
		$str = trim($str);
		if ($str != '' && $str != '*/') {
			trigger_error('Wrong annotation syntax: '.$strBackup.'; Could not parse string: '.$str, E_USER_ERROR);
		}
		
		$annotationName = array_shift($result);
		
		return array(
			'name'			=> $annotationName,
			'parameters'	=> $result
		);
	}
}