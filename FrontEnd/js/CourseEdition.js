//EDICIÓN DE CURSO
//---------------------------------------------------
//---------------------------------------------------
import Utility from '/scripts/Utility.js';


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
               // document.getElementById("cierra_sesion").onclick = confirmacerrar;
               $("#cierra_sesion").on("click", function(){
                    Swal.fire({
                         icon: 'question',
                         title: '<h2 style="color: white;">¿Deseas cerrar sesión?</h2>',
                         showCancelButton: true,
                         cancelButtonText: '<span style="color: #c4c4c4; margin-bottom: 0; font-weight: bolder;">Cancelar</span>',
                         confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">Cerrar sesión</span>',
                         confirmButtonColor: '#48e5c2',
                         cancelButtonColor: 'red',
                         background: '#333333'
                    }).then((result)=>{
                         if (result.isConfirmed){
                              fetch('http://localhost/api/session/', {
                              method: 'DELETE'
                              }).then((response)=>{
                                   if(response.ok){
                                        return response.json(window.location.replace("Inicio.html"));
                                   }
                              })
                         }
                    })
               })
          });



  

    fetch('http://localhost/api/categories/', {
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data)=>{
               console.log(data);
             
               var nav_categories = $("#categorias-colapsables");
               var drop_categories = $("#dropdown_categorias");
               var len = data.length - 5;

            

               //Carga categoria de 1 a 5 en barra de categorias
               for(var i = 0; i < 5; i++){
                   nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html?query=&category='+ data[i].id + '" class="link-barra-categoria">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                  drop_categories.append('<a class="dropdown-item" href="search-results.html?query=&category='+ data[i].id + '">' + data[i].name + '</a>')
               }
          })

     document.getElementById("form_busqueda").addEventListener('submit', (result) => {
          result.preventDefault();
          
          var busca_input = $("#container_buscar").val();

          const form = result.target;
          const info = Utility.formDataToObject(new FormData(form));

          if(busca_input == ''){
               Swal.fire({
                    icon: 'warning',
                    title: '<h2 style="color: white;">Ingresa algo en la barra de búsqueda</h2>',
                    confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
                    confirmButtonColor: '#48e5c2',
                    background: '#333333'
               })
          }else{
               var url_buscar = new URL(`http://localhost/FrontEnd/search-results.html?query`);

               window.location.href = `${url_buscar}=${busca_input}`;

          }

     });



})


$(function(){
    $("#precio_gratis").on("click", function(){
        if($(this).is(":checked")){
            $('#courseCost_input_edit').attr('disabled', true);
            $('#courseCost_input_edit').val('');
        }else{
            $('#courseCost_input_edit').attr('disabled', false);
        }

    });
});

//BOTONES PARA AGREGAR
$("#newCourseLevel").on("click", function(){
          $(".inputs_curso_nivel").append("<div class='accordion'><div class='card'><div class='card-header'><label class='d-inline'> Nombre de nivel: <input id='levelName_input_edit' type='text'><button type='button' id='btn_eliminarNivel_edit' class='btnEliminarNivel'> <i class='fa fa-trash'> </i> </button>  <input style='' type='text' placeholder='Precio'></label></div><div class='card p-3'><button style='font-size: 16px;' type='button' id='newCourselesson_edit' class='btn_nuevaLeccion'><i class='fa fa-plus'></i> Nueva Lección</button></div><ul class='list-unstyled'></ul></div></div>");
});


$(".inputs_curso_nivel").on("click", "#newCourselesson_edit", function(){
          $(".list-unstyled").append("<li class='container_lessonvideo'><hr><i class='fa fa-play-circle mr-2'></i>Nombre de lección: <input class='mb-3' id='lessonName_input_edit' type='text'><button type='button' id='btn_eliminarLeccion_edit' class='btnEliminarLeccion'> <i class='fa fa-trash'> </i> </button><br><span class='ml-4'>Video de lección:</span><input class='mb-3 ml-4'  type='file' name='' id='lessonVideo_input_edit'><br><i class='fa fa-paperclip mr-2'></i>Nombre de archivo: <input class='mb-3' id='levelfileName_input_edit' type='text'><br><span class='ml-4'>Archivo de nivel: </span><input class='mb-3 ml-4' type='file' name='' id='levelFile_input_edit'></li>");
});     
     
// $(".inputs_curso_nivel").on("click", "#newCourseFile_edit", function(){
//           $(".list-unstyled").append("<li class='container_levelfile'><i class='fa fa-paperclip mr-2'></i>Nombre de archivo: <input class='mb-3' id='levelfileName_input_edit' type='text'><button type='button' id='btn_eliminarArchivo_edit' class='btnEliminarArchivo'> <i class='fa fa-trash'> </i> </button><br><span class='ml-4'>Archivo de nivel: </span><input class='mb-3 ml-4' type='file' name='' id='levelFile_input_edit'></li>");
// });


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