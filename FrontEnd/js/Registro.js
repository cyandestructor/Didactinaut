import Utility from '/scripts/Utility.js';

document.getElementById("form_registro").addEventListener("submit", (result) => {
     result.preventDefault();

     //form.files[0] accede a imagen
     //'imagen.type'
     const form = result.target;
     const info = Utility.formDataToObject(new FormData(form));

     fetch('http://localhost/api/users/', {
          method: 'POST',
          headers: {
               'Content-Type': 'application/json'
          },
          body: JSON.stringify(info)
     }).then((response)=>{
          if(response.ok){
               window.alert("¡Usuario registrado! - Iniciemos sesión");
               form.reset();
               return response.json(window.location.replace("IniciaSesion.html"));
          }
     }).then((data)=>{
          console.log(data);
     })

})

