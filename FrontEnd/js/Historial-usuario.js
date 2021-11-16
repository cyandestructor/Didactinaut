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

async function registerCertificate(userId, courseId, callback = null) {
     const url = 'http://localhost/api/certificates/';

     const data = {
          userId: userId,
          courseId: courseId
     }

     const response = await fetch(url, {
          method: 'POST',
          headers: {
               'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
     });

     if (response.ok) {
          const certificate = await response.json();
          
          if (callback) {
               callback(certificate.id);
          }
     }
}

async function cargaInformacionHistorialUsuario() {
     const sessionResponse = await fetch('http://localhost/api/session/');
     const sessionData = await sessionResponse.json();
     
     const id = sessionData.id;
     
     $("#nom_usu_pag").append(sessionData.username);
     $(".image-user-inicio").attr('src', sessionData.avatar);
     
     document.getElementById("cierra_sesion").onclick = confirmacerrar;
     
     // fetch('http://localhost/api/session/', {
     //           method: 'GET'
     //      }).then((response)=>{
     //           if(response.ok){
     //                return response.json();
     //           }
     //      }).then((data)=>{
     //           console.log(data);
     //           id = data.id;
     //      });
     
     
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
     
     var url_historial = new URL(`http://localhost/api/users/${id}/enrollments/`);
     
     var historial_params = {
          count: 10,
          page: 1
     }
     
     url_historial.search = new URLSearchParams(historial_params).toString();
     
     fetch(url_historial, {
          method: 'GET'
     }).then((response)=>{
          if(response.ok){
               return response.json();
          }
     }).then( async (data)=>{
          console.log(data);
          for(var i=0; i<data.length; i++){
               let certificateHtml = '';
               if (data[i].certificateId) {
                    certificateHtml = `<a href="/FrontEnd/Certificate.html?id=${data[i].certificateId}">Ver certificado</a>`;
               }

               const card = document.createElement('div');
               card.classList.add('col-12', 'col-lg-6', 'mb-3');
               card.innerHTML = '<a href=" http://localhost/FrontEnd/course-visor.html?id='+ data[i].id +'" class="section-historial"><section class="card" style="width: 450px;"><div class="row"><div class="col-12 col-lg-5"><img  class="curso-img-section" src="'+ data[i].image +'" alt=""></div><div class="col-12 col-lg-7"><span id="courseTitle-historial">'+ data[i].title +'</span> <br><span id="course-Instructor">'+ data[i].instructor.name + ' ' + data[i].instructor.lastname +'</span><br> <div id="progressBar"></div> <span id="courseProgress">'+ Math.round(Number(data[i].completionRatio) * 100) +'</span>% completado</div></div><hr style="margin-top: 5px; margin-bottom: 5px;"><div class="col-12">Fecha de inscripción: <span>'+ data[i].enrollDate +'</span><br>Última vez en un nivel: <span>'+ data[i].lastTimeChecked +`</span><br></div></section></a>${certificateHtml}`
               $("#container_historial_cursos").append(card);
               if(data[i].completionRatio == 0 ){$("#progressBar").append('<i class="fa fa-battery-0"></i>')}
               if(data[i].completionRatio > 0 && data[i].completionRatio <= .25){$("#progressBar").append('<i class="fa fa-battery-1"></i>')}
               if(data[i].completionRatio > .25 && data[i].completionRatio <= .50){$("#progressBar").append('<i class="fa fa-battery-2"></i>')}
               if(data[i].completionRatio > .50 && data[i].completionRatio <= .75){$("#progressBar").append('<i class="fa fa-battery-3"></i>')}
               if(data[i].completionRatio > .75 && data[i].completionRatio <= 1.00){$("#progressBar").append('<i class="fa fa-battery-4"></i>')}
     
               // Si el usuario completó el curso y no tiene certificado aún
               if (data[i].completionRatio >= 1 && data[i].certificateId === null) {
                    await registerCertificate(id, data[i].id, (id) => {
                         const viewCertificateAnchor = document.createElement('a');
                         viewCertificateAnchor.href = `/FrontEnd/Certificate.html?id=${id}`;
                         viewCertificateAnchor.innerText = 'Ver certificado';

                         card.appendChild(viewCertificateAnchor);
                    });
               }
          }    
     })

}

$(document).ready(function(){
     cargaInformacionHistorialUsuario();
});