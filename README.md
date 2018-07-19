# Creating-new-arbitrary-for-Simpla-CMS
RU: Создание новых произвольных переменных, для их последующего вывода в шаблон

<!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<title>Модуль создание новых произвольных переменных, для их последующего вывода в шаблон для Simpla CMS</title>
	<meta name="description" content="Документация">
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<style>
		html, body {
			padding: 0;
			margin: 0;
		}
		
		html, body, th, td, input, select, textarea {
			font: normal 16px 'Georgia', serif
		}
		
		body, th, td {
			line-height: 1.4;
			color: #333;
		}
		
		h1, h2, h3 {
			color: #333;
			font-weight: 400;
			font-family: 'Vollkorn', 'Georgia', serif;
			margin-bottom: 24px;
		}
		
		h1 {
			color: #333;
			text-decoration: none;
			font-size: 48px;
			letter-spacing: -2px;
		}
		
		h2 { font-size: 36px }
		
		h3 {
			font-size: 24px;
			padding: 10px 20px;
		}
		
		p, ul, ol		{ margin: 15px 20px; list-style: none }
		a, a:visited	{ color: #728FC4 }
		a:hover			{ text-decoration: none }
		.warning		{ color: #f00 }
		
		.container, footer {
			width: 960px;
			margin: 0 auto;
		}
		
		footer {
			padding: 30px 0;
			text-align: center;
			color: #BBB;
			font-size: 12px;
			border-top: 1px solid #bbb;
			margin-top: 50px;
			font-family: arial;
		}
		.line {
			border-bottom: 1px dashed #B9B9B9;
			margin: 0 20px 25px;
			height: 15px;
		}
		
		pre {
			display: block;
			padding: 15px 0 0 20px;
			overflow-x: auto;
			border-radius: 5px;
			background-color: #F6F7F0;
			border: 1px solid #D3D6B7;
			tab-size: 3;
			color: #ADADAD; 
		}
		
		pre .com	{ color: #299B1F }
		pre .add	{ color: #000 }
		
		.f_name {
			font-weight: bold;
			color: #409FEB;
		}
		
		.payment li { margin-bottom: 10px }
		
		.payment span {
			font-style: normal;
			width: 120px;
			float: left;
			text-align: right;
			padding-right: 20px;
			height: 22px;
		}
		
		.payment .yad:first-letter { color: red }
	</style>
</head>
<body>
<div class="container">

	<h2>Установка модуля</h2>

	<p class="warning">Перед установкой модуля, создайте резервную копию сайта и базы данных!</p>
	<p>Скопируйте содержимое папки <span class="f_name">Upload</span> в корневую директорию с установленной Simpla CMS</p>
	
	<p>Наверняка у пользователей бывали случаи когда, например, нужно поменять почту в подвале сайта.
	Сам не разбирается как это сделать через код, а до программиста вечно не дозвонишься и не допишешься.</p>
	<p>
	Или у разработчиков бывали моменты, когда необходимо создать динамическое поле для вывода данных во front. Например номер телефона.  Это нужно было лезть в backand и править как минимум 2 файла.</p>
	<p>
	Сам ни раз с этим сталкивался. Поэтому решил написать следующее дополнение в виде создания произвольных переменных, для их последующего вывода в шаблон, и их редактирование из админки.</p>
	
	<div class="line"></div>
	
	<h3>Подключение модуля</h3>
	
	<p>1. Добавим через phpmyadmin в MySQL базу новый SQL запрос</p>

	<pre>
	<span class="add">CREATE TABLE IF NOT EXISTS `s_newmyvariables` (
	`newmyvariables_id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`label` text NOT NULL,
	PRIMARY KEY (`newmyvariables_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=193 ;


	INSERT INTO `s_newmyvariables` (`newmyvariables_id`, `name`, `label`) VALUES
	(191, 'myvar_phones', 'Телефон'),
	(180, 'myvar_email', 'Почта');</span>
	</pre>

	<div class="line"></div>
	
	<p>2. Зальём файл Newmyvariables.php в папку api/ (он во вложении и в архиве)</p>
	
	<div class="line"></div>
	
	<p>3. Открываем файл api/Simpla.php</p>

	после строки
	<pre>
	<span class="com">'settings' => 'Settings',</span>
	</pre>
	
	пишем
	<pre>
	<span class="com">'newmyvariables'=> 'Newmyvariables', </span>
	</pre>

	<div class="line"></div>
	
	<p>4. Далее открываем simpla/SettingsAdmin.php</p>
		
	после строки	
	<pre>
	<span class="com">$this->design->assign('managers', $managers);</span>
	</pre>
	
	пишем	
	<pre>
	<span class="com">$this->design->assign('newmyvariables', $this->newmyvariables);
	$this->design->assign('myvar', $this->newmyvariables->get_newmyvariables());</span>
	</pre>

	<div class="line"></div>
	

	<p>5. В этом же файле (чуть ниже)</p>
		
	после строки	
	<pre>
	<span class="com">if($this->request->method('POST'))
	{</span>
	</pre>
	
	пишем	
	<pre>
	<span class="com">if (!empty($_POST['new_name']) && !empty($_POST['new_name_label'])) {
    $names[0] = 'myvar_'.$this - > request - > post('new_name');
    $names[1] = $this - > request - > post('new_name_label');
    $this - > newmyvariables - > new_name = $names;
	}

	$this - > design - > assign('myvar', $this - > newmyvariables - > get_newmyvariables());
	foreach($_POST as $key => $value) {
		if (strpos($key, 'myvar') === 0) {
			$this - > settings - > $key = $value;
		}
	}</span>
	</pre>

	<div class="line"></div>
	
	<p>6. И последний файл simpla/design/html/settings.tpl</p>
	
	находим и заменяем (почти в самом конце)
	<pre>
	<span class="com">&lt;input class="button_green button_save" type="submit" name="save" value="Сохранить" /&gt;</span>
	</pre>
	
	пишем	
	<pre>
	<span class="com">
	&lt;!-- Свои переменные --&gt;
	&lt;div class="block layer" id="my-per"&gt;
	   &lt;h2&gt;Новые переменные&lt;/h2&gt;
	   &lt;ul&gt;
		  {foreach from=$myvar key=k item=v}
		  &lt;li style="width: 900px;"&gt;
			 &lt;label class=property&gt;{$v}:&lt;/label&gt;
			 &lt;input name="{$k}" class="simpla_inp" type="text" value="{$settings->$k|escape}" /&gt;
			 &lt;label style="margin-left: 25px;"&gt;{literal}{$settings->{/literal}{$k}{literal}|escape}{/literal}&lt;/label&gt;
		  &lt;/li&gt;
		  {/foreach}
	   &lt;/ul&gt;
	&lt;/div&gt;
	&lt;div class="block"&gt;
	   &lt;h2&gt;Добавление переменной&lt;/h2&gt;
	   &lt;ul&gt;
		  &lt;li&gt;&lt;label class=property&gt;Описание переменной&lt;/label&gt;&lt;input name="new_name_label" class="simpla_inp" type="text" minlength="3" maxlength="20" placeholder="Описание"/&gt;&lt;/li&gt;
		  &lt;li&gt;&lt;label class=property&gt;Имя переменной&lt;/label&gt;&lt;input name="new_name" class="simpla_inp inp2" minlength="6" maxlength="20" placeholder="Уникальное название" type="text" /&gt;&lt;/li&gt;
	   &lt;/ul&gt;
	&lt;/div&gt;
	&lt;input class="button_green button_save" type="submit" id="submit" name="save" value="Сохранить" /&gt;
	&lt;div class="block"&gt;
	   &lt;h2 id="warning"&gt;Переменная с таким именем уже существует!&lt;/h2&gt;
	&lt;/div&gt;
	&lt;!-- Свои переменные (The End)--&gt;
	</span>
	</pre>

	<div class="line"></div>
	
	<p>7. В самом конце файла</p>
	
	после (действительно в самом конце)
	<pre>
	<span class="com">{literal}
	&lt;script&gt;
		$(function() {
			$('#change_password_form').hide();
			$('#change_password').click(function() {
				$('#change_password_form').show();
			});
		}); 
	&lt;/script&gt;
	{/literal} </span>
	</pre>
	
	пишем	
	<pre>
	<span class="com">{literal}
	&lt;script&gt;
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
	&lt;/script&gt;
	{/literal}</span>
	</pre>
	
	

	
</div>

<footer>Copyright © 2018</footer>

</body>
</html>
