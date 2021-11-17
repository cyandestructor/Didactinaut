
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
     return this.optional(element) || /^(?=.*.\d)(?=.*[a-z])(?=.*?[#?!@$%^&*-])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/.test(value);

});


$(document).ready(function(){

    

    $("#form_iniciosesion").validate({
        rules:{
            input: {
                required: true
            },
            password: {
                required: true
            }
        },
        messages:{
            input: {
                required: "Ingresa un correo"
            },
            password:{
                required: "Ingresa la contraseña"
            }
        }
    });
    
    //RANDOM SHORTCUT CTRL + : = GENERATE GETS AND SETS

    $("#form_registro").validate({
        rules:{
            username:{
                required:true
            },
            name:{
                required: true,
                sololetra: true
            },
            lastname:{
                required: true,
                sololetra: true
            },
            gender:{
                required: true,
                sololetra: true
            },
            birthdate:{
                required: true
            },
            email:{
                required: true,
                email: true
            },
            password:{
                required: true,
                formatocontrasenia: true
            }
        },
        messages:{
            username:{
                required:"Ingresa un nombre de usuario"            },
                name:{
                    required: "Ingresa tu nombre(s)",
                    sololetra: "Ingresa sólo letras"
                },
                lastname:{
                    required: "Ingresa tu apellido(s)",
                    sololetra: "Ingresa sólo letras"
                },
                gender:{
                    required: "Ingresa tu género",
                    sololetra: "Ingresa sólo letras"
                },
                birthdate:{
                    required: "Ingresa tu fecha de nacimiento"
                },
                email:{
                    required: "Ingresa tu correo",
                    email: "Ingresa un formato de correo"
                },
                password:{
                    required: "Ingresa la contraseña",
                    formatocontrasenia: "Ingresa: 8 caracteres mín. 1 mayúscula, 1 minúscula, 1 carácter especial, 1 número"
                }
            }
        });
        
        $("#editionForm").validate({
            rules:{
                username:{
                    required: true
                }, 
                name:{
                    required: true,
                    sololetra: true
                },
                lastname:{
                    required: true, 
                    sololetra: true
                    
                },
                email:{
                    required: true,
                    email: true
                    
                },
                
            },
            messages:{
                username:{
                    required:"Ingresa un nombre de usuario"
                },      
                name:{
                    required: "Ingresa tu nombre(s)",
                    sololetra: "Ingresa sólo letras"
                },
                lastname:{
                    required: "Ingresa tu apellido(s)", 
                    sololetra: "Ingresa sólo letras"
                    
                },
                email:{
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
                    formatocontrasenia: "Ingresa: 8 caracteres mín. 1 mayúscula, 1 minúscula, 1 carácter especial, 1 número"
                    
                },
                input_repeat_password:{
                    required: "Ingresa la confirmación de contraseña",
                    equalTo: "Ingresa la misma contraseña anterior"
                    
                }
                
            }
        });
        
        
//----------------------COURSE CREATION-------------------
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

    
    $("#form_crearcategoria").validate({
        rules:{
            name: {
                required: true,
            },
            description: {
                required: true
            }
        },
        messages:{
            name: {
                required: "Ingresa un nombre",
            },
            description:{
                required: "Ingresa una descripción"
            }
        }
    });
    

//----------------------COURSE EDITION-------------------
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




