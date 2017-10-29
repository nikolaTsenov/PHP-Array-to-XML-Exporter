<?php
$xmlArray = [
		//'containerTag' is required for SimpleXmlElementBased Class
		'containerTag' => 'data',
		//'version' not obligatory key (has default value '1.0')
		'version' => '1.0',
		//'encoding' not obligatory key (has default value 'utf-8')
		'encoding' => 'utf-8',
		//all xml tree construction is in key 'tags'
		'tags' => [
			'details' => [
				'meeting' => [
						'notes' => 'Be exact!',
						'date' => 'YYYY-MM-DD',
						'tips' => "Don't forget to smile"
				],
				'company' => 'The Company',
				'companions' => [
						0 => [
								'name' => 'Ivan',
								//construction for a specific xml tag attribute with specific value
								'position' => 'Business Development Manager'
						],
						1 => [
								'name' => 'Stefka',
								'position' => 'Lawyer'
						]
				]
			]
		],
		//put here all common xml attributes with their values, you can leave 'commonTagAttributes' key empty
		'commonTagAttributes' => [
				'company' => [
						'brand' => 'Top'
				],
				'name' => [
						'type' => 'first name',
						'lang' => 'en',
						//as many as you need...
				]
		]
];