# Creating-new-arbitrary-for-Simpla-CMS
RU: Создание новых произвольных переменных, для их последующего вывода в шаблон
Перед установкой модуля, создайте резервную копию сайта и базы данных!

Скопируйте содержимое папки Upload в корневую директорию с установленной Simpla CMS

Наверняка у пользователей бывали случаи когда, например, нужно поменять почту в подвале сайта. Сам не разбирается как это сделать через код, а до программиста вечно не дозвонишься и не допишешься.

Или у разработчиков бывали моменты, когда необходимо создать динамическое поле для вывода данных во front. Например номер телефона. Это нужно было лезть в backand и править как минимум 2 файла.

Сам ни раз с этим сталкивался. Поэтому решил написать следующее дополнение в виде создания произвольных переменных, для их последующего вывода в шаблон, и их редактирование из админки.

Подключение модуля
1. Добавим через phpmyadmin в MySQL базу новый SQL запрос

	CREATE TABLE IF NOT EXISTS `s_newmyvariables` (
	`newmyvariables_id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`label` text NOT NULL,
	PRIMARY KEY (`newmyvariables_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=193 ;


	INSERT INTO `s_newmyvariables` (`newmyvariables_id`, `name`, `label`) VALUES
	(191, 'myvar_phones', 'Телефон'),
	(180, 'myvar_email', 'Почта');
	
2. Зальём файл Newmyvariables.php в папку api/ (он во вложении и в архиве)

3. Открываем файл api/Simpla.php

после строки
	'settings' => 'Settings',
	
пишем
	'newmyvariables'=> 'Newmyvariables', 
	
4. Далее открываем simpla/SettingsAdmin.php

после строки
	$this->design->assign('managers', $managers);
	
пишем
	$this->design->assign('newmyvariables', $this->newmyvariables);
	$this->design->assign('myvar', $this->newmyvariables->get_newmyvariables());
	
5. В этом же файле (чуть ниже)

после строки
	if($this->request->method('POST'))
	{
	
пишем
	if (!empty($_POST['new_name']) && !empty($_POST['new_name_label'])) {
    $names[0] = 'myvar_'.$this - > request - > post('new_name');
    $names[1] = $this - > request - > post('new_name_label');
    $this - > newmyvariables - > new_name = $names;
	}

	$this - > design - > assign('myvar', $this - > newmyvariables - > get_newmyvariables());
	foreach($_POST as $key => $value) {
		if (strpos($key, 'myvar') === 0) {
			$this - > settings - > $key = $value;
		}
	}
	
6. И последний файл simpla/design/html/settings.tpl

находим и заменяем (почти в самом конце)
	<input class="button_green button_save" type="submit" name="save" value="Сохранить" />
	
пишем
	
	<!-- Свои переменные -->
	<div class="block layer" id="my-per">
	   <h2>Новые переменные</h2>
	   <ul>
		  {foreach from=$myvar key=k item=v}
		  <li style="width: 900px;">
			 <label class=property>{$v}:</label>
			 <input name="{$k}" class="simpla_inp" type="text" value="{$settings->$k|escape}" />
			 <label style="margin-left: 25px;">{literal}{$settings->{/literal}{$k}{literal}|escape}{/literal}</label>
		  </li>
		  {/foreach}
	   </ul>
	</div>
	<div class="block">
	   <h2>Добавление переменной</h2>
	   <ul>
		  <li><label class=property>Описание переменной</label><input name="new_name_label" class="simpla_inp" type="text" minlength="3" maxlength="20" placeholder="Описание"/></li>
		  <li><label class=property>Имя переменной</label><input name="new_name" class="simpla_inp inp2" minlength="6" maxlength="20" placeholder="Уникальное название" type="text" /></li>
	   </ul>
	</div>
	<input class="button_green button_save" type="submit" id="submit" name="save" value="Сохранить" />
	<div class="block">
	   <h2 id="warning">Переменная с таким именем уже существует!</h2>
	</div>
	<!-- Свои переменные (The End)-->
	
	
7. В самом конце файла

после (действительно в самом конце)
	{literal}
	<script>
		$(function() {
			$('#change_password_form').hide();
			$('#change_password').click(function() {
				$('#change_password_form').show();
			});
		}); 
	</script>
	{/literal} 
	
пишем
	{literal}
	<script>
		$(function() {
			$('#warning').hide();
			//var input = $('#new-var>input[name="new_name"]');
			$('input[name="new_name"]').on('keyup', function() {
				$('#my-per').find("input").each(function() {
					if ($(this).attr('name') == 'myvar_' + $('input[name="new_name"]').val()) {
						$('#warning').show();
						$("#submit").attr("disabled", "disabled"); // Запрещаем отправку формы
						return false;
					} else {
						$('#warning').hide();
						$("#submit").removeAttr("disabled"); // Запрещаем отправку формы
					}
				});
			})
		})
	</script>
	{/literal}
	
