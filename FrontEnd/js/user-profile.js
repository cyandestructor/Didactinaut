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
               $("#nom_usu_pag").append(data.username);
               $("#nom_usuario").append(data.username);
               $("#img_perfil_usuario").attr('src', data.avatar);
               $(".image-user-inicio").attr('src', data.avatar);
               document.getElementById("cierra_sesion").onclick = confirmacerrar;
          })

     

});

