//CREACION DE CURSO
//---------------------------------------------------
//---------------------------------------------------
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
          $(".inputs_curso_nivel").append("<div class='accordion'><div class='card'><div class='card-header'><label class='d-inline'> Nombre de nivel: <input id='levelName_input' type='text'><button type='button' id='btn_eliminarNivel' class='btnEliminarNivel'> <i class='fa fa-trash'> </i> </button></label></div><div class='card p-3'><button style='font-size: 16px;' type='button' id='newCourselesson' class='btn_nuevaLeccion'><i class='fa fa-plus'></i> Nueva Lección</button><button style='font-size: 16px;' type='button' id='newCourseFile' class='btn_nuevaLeccion'><i class='fa fa-plus'></i> Nuevo archivo</button></div><ul class='list-unstyled'></ul></div></div>");
});


$(".inputs_curso_nivel").on("click", "#newCourselesson", function(){
          $(".list-unstyled").append("<li class='container_lessonvideo'><i class='fa fa-play-circle mr-2'></i>Nombre de lección: <input class='mb-3' id='lessonName_input' type='text'><button type='button' id='btn_eliminarLeccion' class='btnEliminarLeccion'> <i class='fa fa-trash'> </i> </button><br><span class='ml-4'>Video de lección:</span><input class='mb-3 ml-4'  type='file' name='' id='lessonVideo-input'></li>");
});     
     
$(".inputs_curso_nivel").on("click", "#newCourseFile", function(){
          $(".list-unstyled").append("<li class='container_levelfile'><i class='fa fa-paperclip mr-2'></i>Nombre de archivo: <input class='mb-3' id='levelfileName-input' type='text'><button type='button' id='btn_eliminarArchivo'  class='btnEliminarArchivo'> <i class='fa fa-trash'> </i> </button><br><span class='ml-4'>Archivo de nivel: </span><input class='mb-3 ml-4' type='file' name='' id='levelFile-input'></li>");
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
     
$(".inputs_curso_nivel").on("click", ".btnEliminarArchivo", function(){
                         //acceder al elemento padre que lo contiene y eliminarlo con la función remove
                         
                         $(this).parent().remove();
                    
});