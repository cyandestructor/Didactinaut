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
          //  document.getElementById("cierra_sesion").onclick = conf_cerrar;
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
                   nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html" class="link-barra-categoria">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                  drop_categories.append('<a class="dropdown-item" href="search-results.html">' + data[i].name + '</a>')
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
                    container_cursos.append('  <div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data_public[i].id +'"><img src="'+ data_public[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data_public[i].title +'</span> <span id="course-Instructor">'+ data_public[i].instructor.name +'</span><span class="stars"><span style="color: black;">'+ data_public[i].score + '</span> </span></div></a></div>')
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
                    container_cursos.append('  <div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data_sales[i].id +'"><img src="'+ data_sales[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data_sales[i].title +'</span> <span id="course-Instructor">'+ data_sales[i].instructor.name +'</span><span class="stars"><span style="color: black;">'+ data_sales[i].score + '</span> </span></div></a></div>')
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
                    container_cursos.append('  <div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data_calif[i].id +'"><img src="'+ data_calif[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data_calif[i].title +'</span> <span id="course-Instructor">'+ data_calif[i].instructor.name +'</span><span class="stars"><span style="color: black;">'+ data_calif[i].score + '</span> </span></div></a></div>')
               }
          })

     

});



