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
                   nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html" class="link-barra-categoria" id="'+ data[i].id +'">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                  drop_categories.append('<a class="dropdown-item" href="search-results.html">' + data[i].name + '</a>')
               }
          })

     //Cursos más recientes
     var url_calif = new URL('http://localhost/api/courses/');

     var params_calif = {
          count: 4,
          page: 1,
          orderBy: 'publication'
     }

     url_calif.search = new URLSearchParams(params_calif).toString();

     fetch(url_calif, {
          method: 'GET'
     }).then((response)=>{
          if(response.ok){
               return response.json();
          }
     }).then((data)=>{
          console.log(data);
          var container_cursos = $("#cursos_recientes");

          for(var i=0; i<data.length; i++){
               container_cursos.append(' <div class="col-12 col-sm-6 col-md-4 col-lg-3 active"><img src="imgs/course-example-2.jpg" class="img-fluid mx-auto d-block" alt="img1"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">' + data[i].title + '</span><span id="course-Instructor">' + data[i].instructor.name + '</span><span class="stars"><span style="color: black;">4.0</span></span></div></div>')
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