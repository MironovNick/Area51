//var el_img = document.getElementById('image');
//document.getElementById('image').onchange = onFilesSelect;
document.getElementById('image').addEventListener('change', handleFileSelect, false);

// Функция удаления тега для файла картинки в модальном диалоге
// e - параметр, в котором передается id элемента тега
function  DelBtn(e)
	{
		var el1 = document.getElementById(e);
		
		if(el1) {
			el1.remove();
		}
	}
// Функция добавления тега для файла картинки в модальном диалоге
// e - параметр c id input-а с введенным именем тега
var items=1;	// Счетчик для формирования уникального id нового элемента "button"
function  AddTagBtn(e)	
{
	var div = document.getElementById("container_tags"); // Получить элемент - область для добавления тегов
	var input = document.getElementById(e);	// Получить элемент input с введенным именем нового тега
	var tagname = input.value;	// имя добавляемого тега
								// Если имя добавляемого тега не пусто
	if(tagname) {				// - сформировать элемент кнопка, со скрытым input внутри, для передачи на сервер массива имен тегов
		var newtag = "<button id=\"inpbtn" + items + "\" class = \"inpbtn\" onclick=\"DelBtn('inpbtn" + items + "')\">";
		newtag += "<input type=\"hidden\" name=\"inphidd[]\" value=\""+tagname+"\">"+tagname;
		newtag += "<img src=\"close.png\" height=\"15\" width=\"15\" align=\"right\"></button>";
		var newnode=document.createElement("span");	// Создать элемент
		newnode.innerHTML=newtag;					// Занести в него HTML содержимое
		div.appendChild(newnode); // Добавить новый элемент "button" в область
		input.value = "";			// Очистить input для ввода имен тегов
		items++;
	}
}

// Функция добавления в input поиска по тегам - нового имени тега
// e - параметр с именем тега
function  PutInSearch(e)
{
	var inp_search = document.getElementById("inp_search");
	inp_search.value = e;
	
}

//***************** Выбор файла картинки из input
function handleFileSelect(evt) {
    var file = evt.target.files; // FileList object
    var f = file[0];
    // Only process image files.
    if (!f.type.match('image.*')) {
        alert("Image only please....");
    }
    var reader = new FileReader();
    // Closure to capture the file information.
    reader.onload = (function(theFile) {
        return function(e) {
            // Render thumbnail.
            var imageicon = document.getElementById('imageicon');
			imageicon.src = e.target.result;
         };
    })(f);
    // Read in the image file as a data URL.
    reader.readAsDataURL(f);
}

//***************** Выбор файла картинки перетаскиванием drag and drop
// Проверяем поддерживает ли браузер drag and drop
if('ondrop' in document.createElement('div')) {
    onload = function () {
      var dropZone = document.getElementById('drop_box');
 
      /*
       * Обработчик, срабатывающий, когда курсор с
       * перетаскиваем объектом оказывается над dropZone
       */
 
      dropZone.addEventListener('dragover', function (e) {
        // Останавливаем всплытие события
        e.stopPropagation();
        // останавливаем действие по умолчанию, связанное с эти событием.
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';    
      }, false);
 
      /*
       * Обработчик, срабатывающий, когда мы
       * бросаем перетаскиваемые файлы в dropZone
       */
 
      dropZone.addEventListener('drop', function (e) {
 
        e.stopPropagation();
        e.preventDefault();
 
        var file = e.dataTransfer.files;
 
        var f = file[0];
		// Only process image files.
		if (!f.type.match('image.*')) {
			alert("Image only please....");
		}
		var reader = new FileReader();
		// Closure to capture the file information.
		reader.onload = (function(theFile) {
			return function(e) {
				// Render thumbnail.
				var imageicon = document.getElementById('imageicon');
				imageicon.src = e.target.result;
				document.getElementById('image').value = theFile;
			 };
		})(f);
		// Read in the image file as a data URL.
		reader.readAsDataURL(f);
 
      }, false);
    }
  // очень печально если браузер не поддерживает drag and drop
} else {
    //alert("К великой печали ваш браузер не поддерживает Drag&Drop(");
}
