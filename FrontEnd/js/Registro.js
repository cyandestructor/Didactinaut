import Utility from '/scripts/Utility.js';

$(document).ready(function(){

     var opc_creacurso = $("#op_crearcurso");
     var opc_carrito = $("#op_carrito");
     
     opc_creacurso.on("click", function(){
          Swal.fire({
               icon: 'warning',
               title: '<h2 style="color: white;">Inicia sesión para utilizar</h2>',
               confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
               confirmButtonColor: '#48e5c2',
               background: '#333333'
          })
     });

     opc_carrito.on("click", function(){
          Swal.fire({
               icon: 'warning',
               title: '<h2 style="color: white;">Inicia sesión para utilizar</h2>',
               confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">De acuerdo</span>',
               confirmButtonColor: '#48e5c2',
               background: '#333333'
          })     
     });

     
     document.getElementById("form_registro").addEventListener("submit", (result) => {
          result.preventDefault();
     
          //form.files[0] accede a imagen
          //'imagen.type'
          const form = result.target;
          const info = Utility.formDataToObject(new FormData(form));
     
          fetch('http://localhost/api/users/', {
               method: 'POST',
               headers: {
                    'Content-Type': 'application/json'
               },
               body: JSON.stringify(info)
          }).then((response)=>{
               if(response.ok){
                    // window.alert("¡Usuario registrado! - Iniciemos sesión");
                    form.reset();
                    Swal.fire({
                         icon: 'success',
                         title: '<h2 style="color: white;">¡Usuario registrado! - Iniciemos sesión</h2>',
                         confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">¡Vamos!</span>',
                         confirmButtonColor: '#48e5c2',
                         background: '#333333',
                    }).then((result)=>{
                         if (result.isConfirmed){
                                   return response.json(window.location.replace("IniciaSesion.html"));
                              }
                         })
               }
          }).then((data)=>{
               console.log(data);
          })
     
     })


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


});

