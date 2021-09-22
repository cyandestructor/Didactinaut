
//SELECCION DE IMAGEN EN REGISTRO Y EDICIÓN DE USUARIO/ CREACION DE CURSO/ EDICION
//---------------------------------------------------
//---------------------------------------------------
function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_registro')
                    .attr('src', e.target.result)
                    .width(120)
                    .height(120);
                $('#img_curso')
                    .attr('src', e.target.result)
                    .width(120)
                    .height(120);    
            };

            reader.readAsDataURL(input.files[0]);
        }
}


//VALIDACIONES-----------------------------------------
//-----------------------------------------------------
//-----------------------------------------------------
//-----------------------------------------------------
//-----------------------------------------------------

//REGEX VALIDACION DE CORREO
jQuery.validator.methods.email = function(value, element) {
     return this.optional(element) || /^([a-zA-ZÁ-ÿ0-9_]+(?:[.-]?[a-zA-Z0-9]+)@[a-zA-Z0-9]+(?:[.-]?[a-zA-Z0-9]+).[a-zA-Z]{2,7})$/.test(value);
}

//REGEX VALIDACION DE SOLO LETRAS
jQuery.validator.addMethod("sololetra", function(value, element){
     return this.optional(element) || /^[ñÑa-zA-ZÁ-ÿ\s]+$/i.test(value);
}, "Ingrese solo letras");

//REGEX VALIDACION DE CONTRASEÑA
jQuery.validator.addMethod("formatocontrasenia", function(value, element){
     return this.optional(element) || /^(?=.*.\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/.test(value);

});


$(document).ready(function(){

    

    $("#form_iniciosesion").validate({
        rules:{
            correo_inicia_sesion: {
                required: true,
                email: true
            },
            contra_inicia_sesion: {
                required: true
            }
        },
        messages:{
            correo_inicia_sesion: {
                required: "Ingresa un correo",
                email: "Ingresa un formato de correo"
            },
            contra_inicia_sesion:{
                required: "Ingresa la contraseña"
            }
        }
    });

//RANDOM SHORTCUT CTRL + : = GENERATE GETS AND SETS

    $("#form_registro").validate({
        rules:{
            nombres_registro:{
                required: true,
                sololetra: true
            },
            ape_pat_registro:{
                required: true,
                sololetra: true
            },
            ape_mat_registro:{
                required: true,
                sololetra: true
            },
            genero_registro:{
                required: true,
                sololetra: true
            },
            fechaNac_registro:{
                required: true
            },
            correo_registro:{
                required: true,
                email: true
            },
            contra_registro:{
                required: true,
                formatocontrasenia: true
            },
            input_imagen_registro:{
                required: true
            }
        },
        messages:{
            nombres_registro:{
                required: "Ingresa tu nombre(s)",
                sololetra: "Ingresa sólo letras"
            },
            ape_pat_registro:{
                required: "Ingresa tu apellido paterno",
                sololetra: "Ingresa sólo letras"
            },
            ape_mat_registro:{
                required: "Ingresa tu apellido materno",
                sololetra: "Ingresa sólo letras"
            },
            genero_registro:{
                required: "Ingresa tu género",
                sololetra: "Ingresa sólo letras"
            },
            fechaNac_registro:{
                required: "Ingresa tu fecha de nacimiento"
            },
            correo_registro:{
                required: "Ingresa tu correo",
                email: "Ingresa un formato de correo"
            },
            contra_registro:{
                required: "Ingresa la contraseña",
                formatocontrasenia: "Ingresa: 8 caracteres mín. 1 mayúscula, 1 carácter especial, 1 número"
            },
            input_imagen_registro:{
                required: "Elige una imagen de perfil"
            }
        }
    });

    $("#editionForm").validate({
        rules:{
            name:{
                required: true,
                sololetra: true
            },
            lastname_father:{
                required: true, 
                sololetra: true

            },
            lastname_mother:{
                required: true,
                sololetra: true

            },
            genre:{
                required: true,
                sololetra: true

            },
            birthday:{
                required: true

            },
            correo:{
                required: true,
                email: true

            },

        },
        messages:{
            name:{
                required: "Ingresa tu nombre(s)",
                sololetra: "Ingresa sólo letras"
            },
            lastname_father:{
                required: "Ingresa tu apellido paterno", 
                sololetra: "Ingresa sólo letras"

            },
            lastname_mother:{
                required: "Ingresa tu apellido materno",
                sololetra: "Ingresa sólo letras"

            },
            genre:{
                required: "Ingresa tu género",
                sololetra: "Ingresa sólo letras"

            },
            birthday:{
                required: "Ingresa tu fecha de nacimiento"

            },
            correo:{
                required: "Ingresa tu correo",
                email: "Ingresa un formato de correo"

            },

        }
    });

    $("#passwordForm").validate({
        rules:{
            input_password:{
                required: true,
                formatocontrasenia: true

            },
            input_repeat_password:{
                required: true,
                equalTo: "#input_password"

            }

        },
        messages:{
            input_password:{
                required: "Ingresa la contraseña",
                formatocontrasenia: "Ingresa: 8 caracteres mín. 1 mayúscula, 1 carácter especial, 1 número"

            },
            input_repeat_password:{
                required: "Ingresa la confirmación de contraseña",
                equalTo: "Ingresa la misma contraseña anterior"

            }

        }
    });


    $("#form_coursecreation").validate({
        rules:{
            courseTitle_input:{
                required: true
            },
            courseDescription_input:{
                required: true
            }
        },
        messages:{
            courseTitle_input:{
                required: "Ingresa un título al curso"
            },
            courseDescription_input:{
                required: "Ingresa una descripción al curso"
            }
        }

    });

    $("#form_courseEdition").validate({
        rules:{
            courseTitle_input_edit:{
                required: true
            },
            courseDescription_input_edit:{
                required: true
            }
        },
        messages:{
            courseTitle_input_edit:{
                required: "Ingresa un título al curso"
            },
            courseDescription_input_edit:{
                required: "Ingresa una descripción al curso"
            }
        }
    });

});




