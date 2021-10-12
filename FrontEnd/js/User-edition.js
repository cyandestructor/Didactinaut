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
               }
          }).then((data)=>{
               console.log(data);
               var id = data.id;
               
               fetch(`http://localhost/api/users/${id}`
               ).then((response)=>{
                    if(response.ok){
                         return response.json();
                    }
               }).then((dataUser)=>{
                    $("#nom_usu_pag").append(dataUser.username);
                    $("#input_username").val(dataUser.username);
                    $("#input_name").val(dataUser.name);
                    $("#input_lastnames").val(dataUser.lastname);
                    $("#input_descripcion").val(dataUser.description);
                    $("#input_correo").val(dataUser.email);
                    $("#img_registro").attr('src', dataUser.avatar);
                    $(".image-user-inicio").attr('src', data.avatar);
               })

               document.getElementById("cierra_sesion").onclick = confirmacerrar;

 
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
                              window.alert("Imagen actualizada");
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
                              window.alert("Información actualizada");
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
                              window.alert("Contraseña actualizada");
                              return response.json();
                         }
                    })
               
               })

          })     

     

});          

