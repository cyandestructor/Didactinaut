import Utility from '/scripts/Utility.js';

document.getElementById("form_iniciosesion").addEventListener("submit", (result) => {
     result.preventDefault();

     //form.files[0] accede a imagen
     //'imagen.type'
     const form = result.target;
     const info = Utility.formDataToObject(new FormData(form));

     fetch('http://localhost/api/session/', {
          method: 'PUT',
          headers: {
               'Content-Type': 'application/json'
          },
          body: JSON.stringify(info)
     }).then((response)=>{
          if(response.ok){
               form.reset();
               window.alert("¡Bienvenid@ a Didactinaut!");
               return response.json( window.location.replace("SesionIniciada.html"));
              
          }else {
               window.alert("Usuario no encontrado - Vuelve a intentarlo");
          }
     }).then((data)=>{
          console.log(data);
     })

})