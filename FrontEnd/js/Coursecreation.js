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
     
$(".inputs_curso_nivel").on("click", ".btnEliminarArchivo", function(){
                         //acceder al elemento padre que lo contiene y eliminarlo con la función remove
                         
                         $(this).parent().remove();
                    
});