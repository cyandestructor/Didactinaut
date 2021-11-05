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
                   nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html" class="link-barra-categoria">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                  drop_categories.append('<a class="dropdown-item" href="search-results.html">' + data[i].name + '</a>')
               }
          })



});