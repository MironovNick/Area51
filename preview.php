<?php
	session_start();
	$db = mysqli_connect("localhost", "root", "", "intetics_db");
	$user_id = $_SESSION['userid1'];  		// получить ID пользавателя из сессии
	$search_tag = "All";					// По умолчанию не фильтруем картинки по тегам - показываем все (All)

	if (isset($_POST['upload'])) {			// Если пришли данные с формы загрузки файла картинки
		$target = "images/".basename($_FILES['image']['name']); // Путь для копирования файла на сервере


		$image = $_FILES['image']['name']; 	// Имя файла картинки
		$image_text = $_POST['image_text'];	// Текстовое описание картинки из формы
		
		$inphidd =  $_POST['inphidd'];		// массив имен тегов для картинки из формы
		if( count($inphidd) > 0 )				// Если были указаны теги,
			$tags_str = implode(",",$inphidd);	// то сформируем из массива строку, разделив теги ","
		else									// Иначе заносим для картинки
			$tags_str = "All";					//  одно имя тега "All" - все.
		
		if($image) { 				// Если с формы был загружен файл картинки, заносим его имя, описание и список его тегов в БД
			$sql = "INSERT INTO images (image, text, tags, userid) VALUES ('$image', '$image_text', '$tags_str', '$user_id')";
			mysqli_query($db, $sql);
			$search_tag = "All"; 	// Сбрасываем тег фильтра опять в All
			if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) { // Переносим файл картинки на сервер
				echo ('<meta http-equiv="refresh" content="0; URL=preview.php">'); // Если удачно - обновляем страницу
			}else{
				echo "File not uploaded!\n";
			}
		}
	}
	if ( isset($_POST['search_btn']) ) {		// Если пришли данные с формы поиска по тегам
		$search_tag = $_POST['inp_search'];		// - получаем имя тега для поиска 
		if($search_tag === "")					// Если оно пусто
			$search_tag = "All";				// - сбрасываем тег фильтра в All 
	}
	
	$sql = "SELECT image, tags FROM images WHERE userid = '$user_id'";	// SQL запрос
	$res = mysqli_query($db, $sql); // Получаем из БД имена файлов и теги к ним для пользователя "$user_id"
	$tags_all = array();			// Общий массив для тегов пользователя
	$files_all = array();			// Массив для имен файлов пользавателя
	if( $res  ) {									// Если данные из БД получены			
		while ( $row = mysqli_fetch_array($res) ) {	// - читаем их по записям 
			$img_file = $row['image'];				// Имя файла из БД
			// Обработка массива тегов
			if(	$tags_str = $row['tags'] ){			// Строка тегов, разделенных "," из БД
				$tags_arr = explode(",", $tags_str);// Преобразуем строку тегов в массив тегов файла
				if( $search_tag === "All" or $search_tag === "all" or in_array($search_tag, $tags_arr) ) {
					$files_all[] = $img_file;		//Если тег поиска есть в тегах файла или он = "All"
				}									//  - добавляем имя файла в Массив для имен файлов пользователя
					foreach ($tags_arr  as $val) {		// Проходим массив тегов файла
					if( !in_array($val, $tags_all) ) {	// Если в нем есть теги которых еще нет в общем массиве тегов пользователя
						$tags_all[] = $val;				// - Добавляем их туда
					}
				}
				
			}
		}
		sort($tags_all);		// Сортируем Общий массив для тегов пользователя
	}
	// Формируем ассоциативный массив популярности тегов для всех пользователей,
	// отсортированный по количеству раз использования тега:
	// key => val, где key - имя тега,  val - количество раз его использования
	$sql = "SELECT tags FROM images";	// SQL запрос
	$res = mysqli_query($db, $sql);		// Получить из БД все теги
	$tags_eg = array();					// Ассоциативный массив тегов для всех пользователей
	if( $res  ) {
		while ( $row = mysqli_fetch_array($res) ) { // Читаем записи из БД
			if(	$tags_str = $row['tags'] ){			// Из записи получаем поле текстовой строки с тегами
					$tags_arr = explode(",", $tags_str);	// Строку с тегами разделенными "," преобразуем в массив
					foreach ($tags_arr  as $val) {			// Проходим по этому массиву
						if( !in_array($val,array_keys($tags_eg) ) ) { // Если в массиве популярности нет ключа с таким тегом
							$tags_eg[$val] = 1;						// - Добавляем новый элемент с ключом = тегу и значением 1
						} else {									// если есть
							$tags_eg[$val]++;						// - увеличиваем его значение +1
						}
					}
			}
		}
		//krsort($tags_eg);
	}
	
?>

<html>
<head>
 <meta charset="utf-8">
 <title>Main page</title>
 <link rel="stylesheet" href="style.css">
 <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
 </head>

<body>
	<header>
		<div class="container">
		<img src="logo.gif" height="85" width="85">
		<h1>Welcome to Image Previewer</h1>
		<h2>Hi,<?php $name1 = $_SESSION['username1']; echo ("$name1"); ?>!</h2>
		<ul>
		<a href="index.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
		</ul>
		</div>
		<hr color="#cdcdcd", size="3";>

		<form method="POST" action="preview.php" enctype="multipart/form-data">
		<div class="container-search">

		<input list="tag_list" id="inp_search" name="inp_search" autocomplete="off" placeholder="Search by keyword">
		<datalist id="tag_list">
<?php
	foreach($tags_all as $val) {  		// Формируем элементы выпадающего списка тегов для поиска
		echo "<option value='$val'>";	// из Общего массива для тегов пользователя
	}
?>
		</datalist>

		<button type="submit" name="search_btn" class="btn">Search</button>
		</div>
		</form>

		<div class="container-eg">
		<h2>e.g.</h2>
<?php
	$cnt = 0;
	$arr1 = array();
	$arr2 = array();
	foreach($tags_eg as $key => $val) { // Разделяем ассоциативный массив популярности тегов на два:
		$arr1[] = $key;					// массив с ключами
		$arr2[] = $val;					// массив со значениями
	}
	array_multisort($arr2, SORT_DESC, $arr1); // Сортируем массив с ключами по массиву со значениями
	foreach($arr1 as $val) {	// Фомируем 7 элементов с наиболее популярными тегами
		if($cnt < 7)			// добавив каждому обработчик "клика" передав с параметром имя тега 
			echo "<h2><a href=\"#\" onclick=\"PutInSearch('$val')\">$val</a></h2>";
		$cnt++;
	}
?>		
		</div>
	</header>

<div class="container-galery">
	<div class="item">
		<div class="front">
			<img src="addbtn.gif" height="105" width="105" onClick='location.href="#openModal"'>
			<h2>Add new image</h2>
		</div>
	</div>
<?php
	foreach($files_all as $val) {	// Формируем галерею картинок из Массива для имен файлов пользавателя
		echo "<div class='item'>";
		echo "<div>";
		echo "<a data-lightbox='lightbox' href='images/".$val."' >";
		echo "<img class='front' src='images/".$val."' alt=''>";
		echo "</div>";
		echo "</div>";
	}

?>
</div> 

<div id="openModal" class="modalDialog">
	<div class="container-modal">
		<a href="#close" title="Закрыть" class="close"> x</a>
		<form method="POST" action="preview.php" enctype="multipart/form-data">
		<div class="content">
			<div class="column_left"><h1>ADD IMAGE</h1>
				<div id="drop_box" class="drop_box">
					<img id="imageicon" src="imageicon.png" height="110" width="140" >
					<h2>Drag in your media</h2>
					<h3>Find media in your hard drive, then tag them<br>in to automatically upload</h3>
				</div>
				<div class="add_button">
				<h3>Manually selected media</h3>
					<div>
						<input type="file" id="image" name="image">
					</div>
				</div>
			</div>
			<div class="column_right"><h1>ADD INFO</h1>
				<div>
					<h3>Name your media</h3>
					<input type="text" style="width: 300px; height: 35px;">
					<h3>Add tags</h3>
					<div>
						<div id = "container_tags"  class = "container_tags" >
<!--
							<button id="inpbtn1" class = "inpbtn" onclick="DelBtn('inpbtn1')">
							<input type="hidden" name="inphidd[]" value="Man">Man
							<img src="images/close.png" height="15" width="15" style="vertical-align: middle">
							</button>
-->							
						</div>
						<input type="text" id="inptag" maxlength="7" style="width: 200px; height: 35px;" placeholder="Write tag">
						<button type="button" name="addtag_btn" class="btn_addtag" onclick="AddTagBtn('inptag')">Add tag </button>
					</div>		
					<h3>Add description</h3>
					<textarea id="text" cols="30" rows="5" name="image_text"></textarea>
					<button type="submit" name="upload" class="btn_publish">Publish</button>
				</div>
			</div>
	    </div>
		</form>
	</div>
</div>
<script type="text/javascript" src="preview.js"></script>
</body>
</html>

