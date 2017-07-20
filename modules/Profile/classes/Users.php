<?php
namespace Profile;

class Users extends \System\SCRUD {

	public function Init() {
		parent::Init();
		$this->composition = array_merge($this->composition, [
			'PERSONAL' => [
				'NAME'   => 'Личные данные',
				'FIELDS' => [
					'PERSONAL_PROFESSION' => [
						'TYPE' => 'STRING',
						'NAME' => 'Профессия',
					],
					'PERSONAL_WWW'        => [
						'TYPE' => 'STRING',
						'NAME' => 'WWW-страница',
					],
					'PERSONAL_GENDER'     => [
						'TYPE'   => 'ENUM',
						'NAME'   => 'Пол',
						'VALUES' => [
							'MALE'   => 'Мужчина',
							'FEMALE' => 'Женщина',
						],
					],
					'PERSONAL_BIRTHDAY'   => [
						'TYPE' => 'DATE',
						'NAME' => 'Дата рождения',
					],
					'PERSONAL_PHOTO'      => [
						'TYPE' => 'NUMBER',
						'NAME' => 'Фотография',
					],
					'PERSONAL_PHONE'      => [
						'TYPE' => 'STRING',
						'NAME' => 'Телефон',
					],
					'PERSONAL_STREET'     => [
						'TYPE' => 'STRING',
						'NAME' => 'Улица',
					],
					'PERSONAL_MAILBOX'    => [
						'TYPE' => 'STRING',
						'NAME' => 'Почтовый ящик',
					],
					'PERSONAL_CITY'       => [
						'TYPE' => 'STRING',
						'NAME' => 'Город',
					],
					'PERSONAL_STATE'      => [
						'TYPE' => 'STRING',
						'NAME' => 'Область / край',
					],
					'PERSONAL_ZIP'        => [
						'TYPE' => 'STRING',
						'NAME' => 'Индекс',
					],
					'PERSONAL_COUNTRY'    => [
						'TYPE' => 'STRING',
						'NAME' => 'Страна',
					],
					'PERSONAL_NOTES'      => [
						'TYPE' => 'STRING',
						'NAME' => 'Дополнительные заметки',
					],
				],
			],
			'WORK'     => [
				'NAME'   => 'Информация о работе',
				'FIELDS' => [
					'WORK_COMPANY'    => [
						'TYPE' => 'STRING',
						'NAME' => 'Компания',
					],
					'WORK_DEPARTMENT' => [
						'TYPE' => 'STRING',
						'NAME' => 'Отдел',
					],
					'WORK_POSITION'   => [
						'TYPE' => 'STRING',
						'NAME' => 'Должность',
					],
					'WORK_WWW'        => [
						'TYPE' => 'STRING',
						'NAME' => 'Веб-страница',
					],
					'WORK_PHONE'      => [
						'TYPE' => 'STRING',
						'NAME' => 'Телефон',
					],
					'WORK_STREET'     => [
						'TYPE' => 'STRING',
						'NAME' => 'Улица',
					],
					'WORK_MAILBOX'    => [
						'TYPE' => 'STRING',
						'NAME' => 'Почтовый ящик',
					],
					'WORK_CITY'       => [
						'TYPE' => 'STRING',
						'NAME' => 'Город',
					],
					'WORK_STATE'      => [
						'TYPE' => 'STRING',
						'NAME' => 'Область / край',
					],
					'WORK_ZIP'        => [
						'TYPE' => 'STRING',
						'NAME' => 'Индекс',
					],
					'WORK_COUNTRY'    => [
						'TYPE' => 'STRING',
						'NAME' => 'Страна',
					],
					'WORK_LOGO'       => [
						'TYPE' => 'STRING',
						'NAME' => 'Логотип',
					],
					'WORK_NOTES'      => [
						'TYPE' => 'TEXT',
						'NAME' => 'Дополнительные заметки',
					],

				],
			],
		]);
	}

}