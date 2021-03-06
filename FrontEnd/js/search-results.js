import Utility from '/scripts/Utility.js';

function confirmacerrar(){
     var resp = confirm("¿Deseas cerrar sesión?");

     if(resp == true){
          fetch('http://localhost/api/session/', {
               method: 'DELETE'
          })

     }else if (resp == false){
          return false;
     }
}

$(document).ready(function(){

     $('#btnApplyFilter').on('click', function () {
        const category = $('#courseCategorie_input').val();
        const fromDate = $('#dateFrom').val();
        const toDate = $('#dateTo').val();

        const originalParams = new URLSearchParams(location.search);

        const params = {
             query: originalParams.get('query') ?? ''
        };

        if (category !== '') {
             params.category = category;
        }
        if (fromDate !== '') {
             params.from = fromDate;
        }
        if (toDate !== '') {
             params.to = toDate;
        }

        const url = new URL('http://localhost/FrontEnd/search-results.html');
        url.search = new URLSearchParams(params);

        location.href = url;
     });


     fetch('http://localhost/api/session/', {
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }else{
                    //Agrega esta navbar si no hay sesion iniciada
                    var container_no_session = $("#container_sesion_nav");
                    container_no_session.append(' <a class="logo-navbar" href="Inicio.html">Didactinaut</a> <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#contenidocolapsable" aria-controls="contenidocolapsable" aria-expanded="false" aria-label="Toggle navigation"><span class="boton-colapso"> <img src="imgs/Boton-colapso.png" alt=""> </span></button><div class="collapse navbar-collapse " id="contenidocolapsable"><form action="" id="form_busqueda"><input class="buscar" type="text" placeholder="Buscar cursos" id="container_buscar"><a href="search-results.html"><button type="submit" class="boton-buscar"><img src="imgs/Buscar.png" alt=""> </button></a></form><ul class="navbar-nav mr-auto"><li class="nav-item"><a class="opciones-navbar" id="op_crearcurso" href="#">Crear curso</a></li><li class="nav-item"><a href="#" id="op_carrito"><img class="boton-navbar" src="imgs/Carrito.png" alt=""> </a></li><li class="nav-item"><a class="opciones-navbar-2" href="Registro.html">Registrarse</a></li><li class="nav-item"><a class="opciones-navbar-2" style="margin-right: 10px;" href="IniciaSesion.html">Iniciar sesión</a></li></ul></div>')
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
               }
          }).then((data)=>{
               if(data){
                    //Agrega esta navbar si ya hay sesion iniciada y recibe datos
                    var container_session =$("#container_sesion_nav");
                    container_session.append('<a class="logo-navbar" href="SesionIniciada.html">Didactinaut</a><button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#contenidocolapsable" aria-controls="contenidocolapsable" aria-expanded="false" aria-label="Toggle navigation" ><span class="boton-colapso"> <img src="imgs/Boton-colapso.png" alt=""> </span></button><div class="collapse navbar-collapse " id="contenidocolapsable"><form action="" id="form_busqueda"><input class="buscar" type="text" placeholder="Buscar cursos" id="container_buscar"><a href="search-results.html"><button type="submit" class="boton-buscar"><img src="imgs/Buscar.png" alt=""> </button></a></form><ul class="navbar-nav mr-auto"> <li class="nav-item"><a class="opciones-navbar" href="course-creation.html">Crear curso</a></li><li class="nav-item"><a class="opciones-navbar" href="Historial-usuario.html">Historial</a></li><li class="nav-item"><a href="cart.html"><img class="boton-navbar" src="imgs/Carrito.png" alt=""> </a></li><li class="nav-item"><div class="dropdown show"><a class="dropdown-toggle" href="#" role="button" id="dropdown-user-inicio" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="opciones-navbar-2" style="margin-right: 0;" id="nom_usu_pag"> <img class="image-user-inicio" src="https://avatars.dicebear.com/api/bottts/miguel-villanueva.svg?background=%23FF004D" alt=""> </span>  </a><div class="nav-item dropdown-menu" aria-labelledby="dropdown-user-inicio"><a class="dropdown-item" href="MySales.html">Mis ventas</a><a class="dropdown-item" href="user-profile.html">Perfil público</a><a class="dropdown-item" href="user-edition.html">Editar perfil</a><a id="cierra_sesion" class="dropdown-item" href="#" >Cerrar sesión</a></div></div></li></ul></div>')
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
               }
               
          });

          
     fetch('http://localhost/api/categories/', {
          method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data)=>{
               var nav_categories = $("#categorias-colapsables");
               var drop_categories = $("#dropdown_categorias");
               var select_categories = $("#courseCategorie_input");
               var len = data.length - 5;

               //Carga categoria de 1 a 5 en barra de categorias
               for(var i = 0; i < 5; i++){
                    nav_categories.append('<div class="col-6 col-lg-2 ml-4"><a href="search-results.html?query=&category='+ data[i].id + '" class="link-barra-categoria">' + data[i].name + '</a></div>')
               }

               //Carga categorias de la 6 en adelante en dropdown en bara 
               for(var i=5; i < len; i++ ){
                    drop_categories.append('<a class="dropdown-item" href="search-results.html?query=&category='+ data[i].id + '">' + data[i].name + '</a>')
               }

               //Carga categorias al select de la busqueda filtrada
               for(var i=0; i < data.length; i++){
                    select_categories.append('<option value="'+ data[i].id + '">'+ data[i].name + '</option>')
               }
          })

          
          const urlParams = new URLSearchParams(location.search);
          const query = urlParams.get('query');
          const category = urlParams.get('category');
          const firstdate = urlParams.get('from');
          const lastdate = urlParams.get('to');
          
          var url_resultado = new URL(`http://localhost/api/results/`);

          var params_page = {
               query: query ?? '',
               count: 10,
               page: 1
          }

          if(category){
               params_page['category']=category;
          }

          if(firstdate){
               params_page['from']=firstdate;
          }
          if (lastdate) {
               params_page['to']=lastdate;
          }

          url_resultado.search = new URLSearchParams(params_page).toString();

          console.log(url_resultado);
          
          fetch(url_resultado,{
               method: 'GET'
          }).then((response)=>{
               if(response.ok){
                    return response.json();
               }
          }).then((data)=>{
               if(data){
                    console.log(data);
                    var container_resultados_cursos = $("#container_resultados_busqueda");
                    for(var i=0; i<data.length; i++){
                         container_resultados_cursos.append('<div class="col-12 col-sm-6 col-md-4 col-lg-3"><a class="courselink-inicio" href="http://localhost/FrontEnd/course-details.html?id='+ data[i].id +'"><img src="'+ data[i].image +'" class="img-fluid mx-auto d-block" alt="img2"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">'+ data[i].title +'</span> <span id="course-Instructor">Instructor: '+ data[i].instructor.name +'</span><span class="stars"><span style="color: black;" id="score_course"></span> </span></div></a></div>')
                         if(data[i].score == null){container_resultados_cursos.children().last().find("#score_course").append('No hay reseñas disponibles');}
                         if(data[i].score == 1){
                              container_resultados_cursos.children().last().find("#score_course").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                         }
                         if(data[i].score == 2){
                              container_resultados_cursos.children().last().find("#score_course").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                         }
                         if(data[i].score == 3){
                              container_resultados_cursos.children().last().find("#score_course").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>');
                         }
                         if(data[i].score == 4){
                              container_resultados_cursos.children().last().find("#score_course").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>');
                         }
                         if(data[i].score == 5){
                              container_resultados_cursos.children().last().find("#score_course").append(data_calif[i].score + ' <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>');
                         }
                         }
               }
          })

});