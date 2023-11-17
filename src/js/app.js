let paso = 1;

const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}


document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp(){
    mostrarSeccion(); //muestra y oculta las secciones
    tabs();// Cambia la seccion cuando se presionen los tabs
    botonesPaginador(); // agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI();
    idCliente();//añade el id del cliente en el objeto
    nombreCliente(); //añade el nombre del cliente al objeto de cita
    seleccionaFecha();//añade la fecha de la cita en el objeto
    seleccionarHora();//añade la hora de la cita en el objeto

    mostrarResumen();// muestra el resumen de la cita
}

function mostrarSeccion() {  
    //oculta la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar')
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    //seleccionar la seccion con el paso...
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    //quita la clase de actual al tab anterior

    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    //Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs(){
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton =>{
        boton.addEventListener('click', function(e){
            paso = parseInt( e.target.dataset.paso);
            mostrarSeccion();

            botonesPaginador();
        });
    });
}

function botonesPaginador(){
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }else if(paso === 3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    }else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior(){
const paginaAnterior = document.querySelector('#anterior');
paginaAnterior.addEventListener('click',function(){
    if(paso <= pasoInicial) return;

    paso--;

    botonesPaginador();
});
}

function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click',function(){
        if(paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
    });
}

async function consultarAPI(){
    try {
        const url = '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
       mostrarServicios(servicios);
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios){
    servicios.forEach( servicio => {
        const {id, nombre, precio} = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;
        
        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio') ;
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function(){ //al dar click en el div, se selecciona los datos del servicio
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);
        
        document.querySelector('#servicios').appendChild(servicioDiv); //inyeccion de datos
    });
}

function seleccionarServicio(servicio){
    const {id} = servicio;
    const {servicios} = cita;

    //identificar al elemento al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    //comprobar si  un servicio ya fue agregado
    if( servicios.some( agregado => agregado.id ===  id)){
        //eliminarlo
        cita.servicios = servicios.filter( agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');
    }else{
        //agregarlo a la lista de servicios
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }

//   console.log(cita);
}
//añade el id del cliente en el objeto
function idCliente(){
    cita.id  = document.querySelector('#id').value; 
}

//añade el nombre del cliente
function nombreCliente(){
    const nombre = document.querySelector('#nombre').value;
    cita.nombre = nombre;
    
}

//seleccionar la fecha
function  seleccionaFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){

        const dia = new Date(e.target.value).getUTCDay();
        if([6,0].includes(dia)){
            mostrarAlerta('Fines de semana no es permitido', 'error', '.formulario');
            e.target.value = '';

        }

        cita.fecha = e.target.value;
    });
}

//seleccionar hora
function seleccionarHora(){
    const inputhora = document.querySelector('#hora');
    inputhora.addEventListener('input',function(e){
        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];

        if(hora < 10 || hora > 18){
            mostrarAlerta('hora no valida','error', '.formulario');
            e.target.value = '';
        }else{
            console.log('hora valida');
            cita.hora = e.target.value;
        }
        console.log(cita);
    });
}

//mostrar las alertas
function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) { 

    const alertaPrevia =document.querySelector('.alerta');
    if(alertaPrevia){
        alertaPrevia.remove();
    }

    //scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const refencia = document.querySelector(elemento);
    refencia.appendChild(alerta);
    

    if(desaparece){
        //tiempo para eliminar la alerta
        setTimeout(()=>{
            alerta.remove();
        },3000);
    }
 }

//seccion del resumen
 function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    //Limpiar el contenido del resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }
    if(Object.values(cita).includes('')|| cita.servicios.length === 0){
        mostrarAlerta('faltan datos de servicios, fecha u hora', 'error','.contenido-resumen', false);

        return;
    }

    //formatear el div de resumen

    const {nombre, fecha, hora, servicios} = cita;
   
    //heading para servicios de resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent= "Resumen de Servicios";
    resumen.appendChild(headingServicios);

    //iterando y mostrando los servicios
    servicios.forEach(servicio =>{
        const {id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add("contenedor-servicio");

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML =`<span>Precio: </span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    //heading para cita de resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent= "Resumen de Cita";
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    //formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 1;// el +1 es porque el dia esta desfasado 
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(year, mes, dia);

    const opciones = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);
    
    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    //boton para crear una cita

    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(horaCita);
    resumen.appendChild(fechaCita);

    resumen.appendChild(botonReservar);
 }

 async function reservarCita(){ //para usar await es necesario que la funcion sea async
    const {id, fecha, hora, servicios} = cita;
    
    //recordar que: el foreach itera mientras que el map encuentra las coincidencia y las guarda
    const idServicio = servicios.map( servicio => servicio.id);

    // console.log(idServicio);

    const datos = new FormData();
    // datos.append('nombre', 'luis');//append es la forma de agregar datos en el formData()
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicio);

    // console.log([...datos])
    
try {
     //peticion hacia la api
     
    //  const url = '${location.origin}/api/citas';
     const url = '/api/citas';

     const respuesta = await fetch (url, {
         method: 'POST',
         body: datos
     });
 
     const resultado = await respuesta.json();
     console.log(resultado.resultado);
 
     if(resultado.resultado){
         Swal.fire({
             icon: 'success',
             title: 'Cita Creada',
             text: 'Tu cita fue creada correctamente!',
             button: 'OK!'
           }).then( () => {
            setTimeout(()=>{
                window.location.reload();
            },1000); 
           });
     }
} catch (error) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Hubo un error al guardar la cita!',
      })
}
   

    // console.log([...datos]) //esto lo que hace es tomar una copia del formData y lo formatea y te devuelve un array
 }

 