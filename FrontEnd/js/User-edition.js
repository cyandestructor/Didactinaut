import Utility from '/scripts/Utility.js';


async function cargarInformacionUsuario() {
     const sessionResponse = await fetch('http://localhost/api/session/');
     const sessionData = await sessionResponse.json();
     
     const id = sessionData.id;
     
     const userResponse = await fetch(`http://localhost/api/users/${id}`);
     const userData = await userResponse.json();

     $("#nom_usu_pag").append(userData.username);
     $("#input_username").val(userData.username);
     $("#input_name").val(userData.name);
     $("#input_lastnames").val(userData.lastname);
     $("#input_descripcion").val(userData.description);
     $("#input_correo").val(userData.email);
     $("#img_registro").attr('src', userData.avatar);
     $(".image-user-inicio").attr('src', sessionData.avatar);

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

     document.getElementById("imageForm").addEventListener("submit", (resultI) => {
          resultI.preventDefault();

          //form.files[0] accede a imagen
          //'imagen.type'
          var inputimg = document.getElementById("input_imagen_registro");
          const form = resultI.target;
          const img = inputimg.files[0];

          fetch(`http://localhost/api/users/${id}/image`, {
               method: 'PUT',
               headers: {
                    'Content-Type': img.type
               },
               body: img
          }).then((response)=>{
               if(response.ok){
                    // window.alert("Imagen actualizada");
                    Swal.fire({
                         icon: 'success',
                         title: '<h2 style="color: white;">Imagen actualizada</h2>',
                         confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">¡Vamos!</span>',
                         confirmButtonColor: '#48e5c2',
                         showConfirmButton: false,
                         timer: 1200,
                         timerProgressBar: true,
                         didOpen: (toast) => {
                         toast.addEventListener('mouseenter', Swal.stopTimer)
                         toast.addEventListener('mouseenter', Swal.resumeTimer)
                         },
                         background: '#333333',
                    })
                    return;
               }
          })

          
     })
     document.getElementById("editionForm").addEventListener("submit", (result) => {
          result.preventDefault();
          
          const form = result.target;
          const info = Utility.formDataToObject(new FormData(form));
          
          fetch(`http://localhost/api/users/${id}`, {
               method: 'PUT',
               headers: {
                    'Content-Type': 'application/json'
               },
               body: JSON.stringify(info)
          }).then((response)=>{
               if(response.ok){
                    // window.alert("Información actualizada");
                    Swal.fire({
                         icon: 'success',
                         title: '<h2 style="color: white;">Información actualizada</h2>',
                         confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">¡Vamos!</span>',
                         confirmButtonColor: '#48e5c2',
                         showConfirmButton: false,
                         timer: 1200,
                         timerProgressBar: true,
                         didOpen: (toast) => {
                         toast.addEventListener('mouseenter', Swal.stopTimer)
                         toast.addEventListener('mouseenter', Swal.resumeTimer)
                         },
                         background: '#333333',
                    })
                    return response.json();
               }
          })
     })     
     document.getElementById("passwordForm").addEventListener("submit", (resultP) => {
          resultP.preventDefault();
     
          const formPasword = resultP.target;
          const infoPasword = Utility.formDataToObject(new FormData(formPasword));
     
          fetch(`http://localhost/api/users/${id}`, {
               method: 'PUT',
               headers: {
                    'Content-Type': 'application/json'
               },
               body: JSON.stringify(infoPasword)
          }).then((response)=>{
               if(response.ok){
                    formPasword.reset();
                    // window.alert("Contraseña actualizada");
                    Swal.fire({
                         icon: 'success',
                         title: '<h2 style="color: white;">Contraseña actualizada</h2>',
                         confirmButtonText: '<span style="color: #333333; margin-bottom: 0;">¡Vamos!</span>',
                         confirmButtonColor: '#48e5c2',
                         showConfirmButton: false,
                         timer: 1200,
                         timerProgressBar: true,
                         didOpen: (toast) => {
                         toast.addEventListener('mouseenter', Swal.stopTimer)
                         toast.addEventListener('mouseenter', Swal.resumeTimer)
                         },
                         background: '#333333',
                    })
                    return response.json();
               }
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
}

$(document).ready(function(){
     cargarInformacionUsuario();
});          

