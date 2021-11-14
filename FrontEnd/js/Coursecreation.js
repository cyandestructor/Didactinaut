//CREACION DE CURSO
//---------------------------------------------------
//---------------------------------------------------
import Utility from '/scripts/Utility.js';


function confirmacerrar(){
     var resp = confirm("¿Deseas cerrar sesión?");

     if(resp == true){
          fetch('http://localhost/api/session/', {
               method: 'DELETE'
          })

     }else if (resp == false){
          return false;
     }
}

$(document).ready(function(){
    
    fetch('http://localhost/api/session/', {
         method: 'GET'
     }).then((response)=>{
          if(response.ok){
               return response.json();
          }
     }).then((data)=>{
          console.log(data);
          $("#nom_usu_pag").append(data.username);
          $(".image-user-inicio").attr('src', data.avatar);
          document.getElementById("cierra_sesion").onclick = confirmacerrar;
     });
     


    document.getElementById("form_crearcategoria").addEventListener("submit", (result) => {
        result.preventDefault();
    
         const form = result.target;
         const info = Utility.formDataToObject(new FormData(form));
         var titulo_cat = info.name.trim();
         var desc_cat = info.description.trim();

     //  console.log(info);
     // console.log(desc_cat);
     //  return;
     if(titulo_cat != '' && desc_cat != ''){
          fetch('http://localhost/api/categories/', {
               method: 'POST',
               headers: {
                    'Content-Type': 'application/json'
               },
               body: JSON.stringify(info)
          }).then((response)=>{
                //     window.alert("Categoría creada!");
               if(response.ok){
                    form.reset();
                    Swal.fire({
                         icon: 'success',
                         title: '<h2 style="color: white;">Categoría creada</h2>',
                         confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">¡Vamos!</span>',
                         confirmButtonColor: '#48e5c2',
                         showConfirmButton: false,
                         timer: 1200,
                         timerProgressBar: true,
                         didOpen: (toast) => {
                              toast.addEventListener('mouseenter', Swal.stopTimer)
                              toast.addEventListener('mouseenter', Swal.resumeTimer)
                         },
                         background: '#333333',
                    })

               }
          })
     }
    })

    document.getElementById("form_coursecreation").addEventListener("submit", (result) => {
          result.preventDefault();

          const form = result.target;
          const info = Utility.formDataToObject(new FormData(form));

    });

    fetch('http://localhost/api/categories/', {
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data)=>{
               console.log(data);
               var select_categories = $("#categoria_container");
               var nav_categories = $("#categorias-colapsables");
               var drop_categories = $("#dropdown_categorias");
               var len = data.length - 5;

               //Carga categorias en dropdown de crear curso
               for(var i=0; i < data.length; i++){
                   select_categories.append('<option value="' + data[i].id + '">' + data[i].name + '</option>') ; 
               }

               //Carga categoria de 1 a 5 en barra de categorias
               for(var i = 0; i < 5; i++){
                   nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html" class="link-barra-categoria">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                  drop_categories.append('<a class="dropdown-item" href="search-results.html">' + data[i].name + '</a>')
               }
          })



})


$(function(){
    $("#precio_gratis").on("click", function(){
        if($(this).is(":checked")){
            $('#courseCost_input').attr('disabled', true);
            $('#courseCost_input').val('');
        }else{
            $('#courseCost_input').attr('disabled', false);
        }

    });
});

//BOTONES PARA AGREGAR
$("#newCourseLevel").on("click", function(){
    const html = "<div class='accordion level-form'><div class='card'><div class='card-header'><label class='d-inline'> Nombre de nivel: <input id='levelName_input' class='level-name-input' type='text'><button type='button' id='btn_eliminarNivel' class='btnEliminarNivel'> <i class='fa fa-trash'> </i> </button> <input style='' class='level-price-input' type='text' placeholder='Precio'> </label></div><div class='card p-3'><button style='font-size: 16px;' type='button' id='newCourselesson' class='btn_nuevaLeccion btn-new-lesson'><i class='fa fa-plus'></i> Nueva Lección</button></div><ul id='lessons-container' class='list-unstyled'></ul></div></div>";
    $(".inputs_curso_nivel").append(html);
});

$(".inputs_curso_nivel").on("click", ".btn-new-lesson", function(){
    $(this).parent().parent().find('#lessons-container').append("<li class='container_lessonvideo lesson-form'><i class='fa fa-play-circle mr-2'></i>Nombre de lección: <input class='mb-3 lesson-title-input' id='lessonName_input' type='text'> <button type='button' id='btn_eliminarLeccion' class='btnEliminarLeccion'> <i class='fa fa-trash'> </i> </button><br> <span class='ml-4'>Video de lección:</span><input class='mb-3 ml-4 video-input'  type='file' name='' id='lessonVideo-input'><div class='card p-3'><button style='font-size: 16px;' type='button' id='newCourseFile' class='btn_nuevaLeccion btn_new_file'><i class='fa fa-plus'></i> Nuevo archivo</button><ul class='list-unstyled' id='files-container'></ul></div></li>");
});
     
$(".inputs_curso_nivel").on("click", ".btn_new_file", function(){
    $(this).parent().find('#files-container').append("<li class='container_levelfile file-form'><i class='fa fa-paperclip mr-2'></i>Nombre de archivo: <input class='mb-3 file-name-input' id='levelfileName-input' type='text'><button type='button' id='btn_eliminarArchivo'  class='btnEliminarArchivo'> <i class='fa fa-trash'> </i> </button><br><span class='ml-4'>Archivo de nivel: </span><input class='mb-3 ml-4 file-input' type='file' name='' id='levelFile-input'></li>");
});


//BOTONES PARA ELIMINAR 
$(".inputs_curso_nivel").on("click", ".btnEliminarLeccion", function(){
                         //acceder al elemento padre que lo contiene y eliminarlo con la función remove
                         
                         $(this).parent().remove();
                        
});
     
$(".inputs_curso_nivel").on("click", ".btnEliminarNivel", function(){
                         //acceder al elemento padre que lo contiene y eliminarlo con la función remove
                         
                         $(this).parent().parent().parent().parent().remove();
                    
});
     
// $(".inputs_curso_nivel").on("click", ".btnEliminarArchivo", function(){
//                          //acceder al elemento padre que lo contiene y eliminarlo con la función remove
                         
//                          $(this).parent().remove();
                    
// });