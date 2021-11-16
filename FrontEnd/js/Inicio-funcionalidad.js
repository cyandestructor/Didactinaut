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
                   nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html?query=&category='+ data[i].id + '" class="link-barra-categoria" id="'+ data[i].id +'">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                  drop_categories.append('<a class="dropdown-item" href="search-results.html?query=&category='+ data[i].id + '">' + data[i].name + '</a>')
               }
          })

          //Cursos más recientes
          var url_public = new URL('http://localhost/api/courses/');

          var params_public = {
               count: 4,
               page: 1,
               orderBy: 'publication'
          }

          url_public.search = new URLSearchParams(params_public).toString();

          fetch(url_public, {
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data_public)=>{
               console.log(data_public);
               var container_cursos = $("#cursos_recientes");

               for(var i=0; i<data_public.length; i++){
                    container_cursos.append('  <div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data_public[i].id +'"><img src="'+ data_public[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data_public[i].title +'</span> <span id="course-Instructor">Instructor: '+ data_public[i].instructor.name +'</span><span class="stars"><span style="color: black;" id="score_course_public"></span> </span></div></a></div>')
                    if(data_public[i].score == null){$("#score_course_public").append('No hay reseñas disponibles');}
                    if(data_public[i].score == 1){
                         $("#score_course_public").append(data_public[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_public[i].score == 2){
                         $("#score_course_public").append(data_public[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_public[i].score == 3){
                         $("#score_course_public").append(data_public[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_public[i].score == 4){
                         $("#score_course_public").append(data_public[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_public[i].score == 5){
                         $("#score_course_public").append(data_public[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');
                    }
               }
          })

            //Cursos más vendidos
          var url_sales = new URL('http://localhost/api/courses/');

          var params_sales = {
               count: 4,
               page: 1,
               orderBy: 'sales'
          }

          url_sales.search = new URLSearchParams(params_sales).toString();

          fetch(url_sales, {
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data_sales)=>{
               console.log(data_sales);
               var container_cursos = $("#cursos_vendidos");

               for(var i=0; i<data_sales.length; i++){
                    container_cursos.append('  <div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data_sales[i].id +'"><img src="'+ data_sales[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data_sales[i].title +'</span> <span id="course-Instructor">Instructor: '+ data_sales[i].instructor.name +'</span><span class="stars"><span style="color: black;" id="score_course_sales"></span> </span></div></a></div>')
                    if(data_sales[i].score == null){$("#score_course_sales").append('No hay reseñas disponibles');}
                    if(data_sales[i].score == 1){
                         $("#score_course_sales").append(data_sales[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_sales[i].score == 2){
                         $("#score_course_sales").append(data_sales[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_sales[i].score == 3){
                         $("#score_course_sales").append(data_sales[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_sales[i].score == 4){
                         $("#score_course_sales").append(data_sales[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_sales[i].score == 5){
                         $("#score_course_sales").append(data_sales[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');
                    }
               }
          })

            //Cursos mejor calificados
          var url_calif = new URL('http://localhost/api/courses/');

          var params_calif = {
               count: 4,
               page: 1,
               orderBy: 'score'
          }

          url_calif.search = new URLSearchParams(params_calif).toString();

          fetch(url_calif, {
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data_calif)=>{
               console.log(data_calif);
               var container_cursos = $("#cursos_calificados");

               for(var i=0; i<data_calif.length; i++){
                    container_cursos.append('  <div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data_calif[i].id +'"><img src="'+ data_calif[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data_calif[i].title +'</span> <span id="course-Instructor">Instructor: '+ data_calif[i].instructor.name +'</span><span class="stars"><span style="color: black;" id="score_course_calif"></span> </span></div></a></div>')
                    if(data_calif[i].score == null){$("#score_course_calif").append('No hay reseñas disponibles');}
                    if(data_calif[i].score == 1){
                         $("#score_course_calif").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_calif[i].score == 2){
                         $("#score_course_calif").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_calif[i].score == 3){
                         $("#score_course_calif").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_calif[i].score == 4){
                         $("#score_course_calif").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
                    }
                    if(data_calif[i].score == 5){
                         $("#score_course_calif").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');
                    }
                    
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