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
                    container_session.append('<a class="logo-navbar" href="SesionIniciada.html">Didactinaut</a><button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#contenidocolapsable" aria-controls="contenidocolapsable" aria-expanded="false" aria-label="Toggle navigation" ><span class="boton-colapso"> <img src="imgs/Boton-colapso.png" alt=""> </span></button><div class="collapse navbar-collapse " id="contenidocolapsable"><form action="" id="form_busqueda"><input class="buscar" type="text" placeholder="Buscar cursos" id="container_buscar"><a href="search-results.html"><button type="submit" class="boton-buscar"><img src="imgs/Buscar.png" alt=""> </button></a></form><ul class="navbar-nav mr-auto"> <li class="nav-item"><a class="opciones-navbar" href="course-creation.html">Crear curso</a></li><li class="nav-item"><a class="opciones-navbar" href="Historial-usuario.html">Historial</a></li><li class="nav-item"><a href="cart.html"><img class="boton-navbar" src="imgs/Carrito.png" alt=""> </a></li><li class="nav-item"><div class="dropdown show"><a class="dropdown-toggle" href="#" role="button" id="dropdown-user-inicio" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="opciones-navbar-2" style="margin-right: 0;" id="nom_usu_pag"> <img class="image-user-inicio" src="https://avatars.dicebear.com/api/bottts/miguel-villanueva.svg?background=%23FF004D" alt=""> </span>  </a><div class="nav-item dropdown-menu" aria-labelledby="dropdown-user-inicio"><a class="dropdown-item" href="MySales.html">Mis ventas</a><a class="dropdown-item" href="user-profile.html">Perfil público</a><a class="dropdown-item" href="user-edition.html">Editar perfil</a><a id="cierra_sesion" class="dropdown-item" href="Inicio.html" >Cerrar sesión</a></div></div></li></ul></div>')
                    $("#nom_usu_pag").append(data.username);
                    $(".image-user-inicio").attr('src', data.avatar);
                    document.getElementById("cierra_sesion").onclick = confirmacerrar;
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

          if(firstdate && lastdate){
               params_page['from']=firstdate;
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
                    for(var i=0; i<data.length; i++){
                         $("#container_resultados_busqueda").append(' <div class="col-12 col-sm-6 col-md-4 col-lg-3 active"><img src="imgs/course-example-2.jpg" class="img-fluid mx-auto d-block" alt="img1"><div class="card" style="height: 100px;"><span id="courseTitle-inicio">' + data[i].title + '</span> <span id="course-Instructor">' + data[i].instructor.name + '</span><span class="stars"><span style="color: black;">4.0</span> </span></div></div>')
                    }
               }
          })

});