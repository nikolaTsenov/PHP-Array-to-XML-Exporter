<?php
$data = [
	//'version' not obligatory key (has default value '1.0')
	'version' => '1.0',
	//'encoding' not obligatory key (has default value 'utf-8')
	'encoding' => 'utf-8',
	//all xml tree construction is in key 'tags'
	'tags' => [
		'meeting' => [
			'notes' => 'Be exact!',
			'date' => 'YYYY-MM-DD',
			'tips' => "Don't forget to smile",
			//construction for nested tags
			'dresscode' => [
				'style' => 'Hawai'
			],
			'hour' => '12:30 am',
			//construction for repeating repeating tags with same name 
			0 => [
				'subject' => [
					'subjectid' => 1,
					'subjectname' => 'New business investments',
					'duration' => '2.00 h',
					'tension' => 'high',
					'materials' => [
						// construction for bottom tags
						'materialid' => 'kjashfbkasdjbffka-adhksfbdlas-dasfdasf-asdfdfas',
						'materialtype' => 'pp presentation',
						'materialsubject' => 'New possible investments in the local region'
					]
				]
			],
			1 => [
				'subject' => [
					'subjectid' => 2,
					'subjectname' => 'Law issues',
					'duration' => '1.00 h',
					'tension' => 'middle',
					'materials' => [
						'materialid' => 'adsasd-asdasd-asdasd-asdasd',
						'materialtype' => 'pp presentation',
						'materialsubject' => 'Possible law obstacles'
					]
				]
			]
		],
		'company' => 'The Company',
		'companions' => [
			0 => [
				'name' => 'Ivan',
				//construction for a specific xml tag attribute with specific value
				'position(rank:manager;value:high)' => 'Business Development Manager'
			],
			1 => [
				'name' => 'Stefka',
				'position(rank:employee;value:high;education:law)' => 'Lawyer'
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
