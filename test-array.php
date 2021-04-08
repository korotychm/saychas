<?php
	$data = [ ['id'=>96, 'shipping_no'=>'212755-1', 'part_no'=>'reterty'], ['id'=>96, 'shipping_no'=>'212755-1', 'part_no'=>'qqqqqq'], ['id'=>97, 'shipping_no'=>'212755-2', 'part_no'=>'wwwwww']  ];
	$header = [ 'col1', 'col2', 'col3' ];
	$hd	= [ [ '111', '222', '333' ],
		    [ '111', '222', '3333' ],
		    [ '222', '22222', '33333' ],  ];
	print_r($data);
	$result = [];
	foreach ($data as $key => $element) {
		$id = $element['id'];
		unset($element['id']);
		$result[$id][] = $element;
		//$result[$element['id']][] = $element;
	}

	var_dump($result);
