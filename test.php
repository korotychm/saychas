<?php
	class Filter
	{
		private $suffix = '';
		public function __construct($suffix='')
		{
			$this->suffix = $suffix;
		}
		public function __invoke($str)
		{
			echo $str.' '.$this->suffix."\n";
		}
	}

	$param = isset($argv[1]) ? $argv[1] : '';
	$filter = new Filter($param);
	$filter('banzaii');
