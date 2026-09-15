const info={
inicio:["Inicio","Todo lo importante, en un solo lugar."],
calendario:["Calendario","Consulta tus actividades por fecha."],
recordatorios:["Recordatorios","Organiza tus tareas y pendientes."],
notas:["Notas","Guarda ideas y apuntes importantes."]
};

const buttons=document.querySelectorAll(".nav-btn");
const views=document.querySelectorAll(".view");

function showView(name){
  if(!info[name]) name="inicio";
  views.forEach(v=>v.classList.toggle("active",v.id===name));
  buttons.forEach(b=>b.classList.toggle("active",b.dataset.view===name));
  document.getElementById("title").textContent=info[name][0];
  document.getElementById("subtitle").textContent=info[name][1];
  history.replaceState(null,"","#"+name);
}

buttons.forEach(button=>button.addEventListener("click",()=>showView(button.dataset.view)));
document.querySelectorAll("[data-go]").forEach(button=>button.addEventListener("click",()=>showView(button.dataset.go)));

/* ALERTAS DE LA FASE ESTATICA */
document.getElementById("newReminder").addEventListener("click",function(){
  window.alert("Esta función pertenece a la ruta dinámica.\n\nAquí podrás crear, editar y eliminar recordatorios usando PHP y PostgreSQL.");
});

document.getElementById("newNote").addEventListener("click",function(){
  window.alert("Esta función pertenece a la ruta dinámica.\n\nAquí podrás crear, editar y eliminar notas y guardarlas en PostgreSQL.");
});

/* MODO DIA / NOCHE */
const themeToggle=document.getElementById("themeToggle");
const themeIcon=document.getElementById("themeIcon");
const savedTheme=localStorage.getItem("agendalocal-theme");
if(savedTheme==="light") document.body.classList.add("light");

function updateTheme(){
  const light=document.body.classList.contains("light");
  themeIcon.textContent=light?"☀":"☾";
  themeToggle.setAttribute("aria-label",light?"Cambiar a modo nocturno":"Cambiar a modo día");
}
themeToggle.addEventListener("click",()=>{
  document.body.classList.toggle("light");
  localStorage.setItem("agendalocal-theme",document.body.classList.contains("light")?"light":"dark");
  updateTheme();
});
updateTheme();

/* CALENDARIO MULTIMES */
const months=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
const weekdays=["Lun","Mar","Mié","Jue","Vie","Sáb","Dom"];
const demoEvents={
"2026-09-03":[["Revisión de avances","10:00","medium"]],
"2026-09-09":[["Entrega de documento","14:30","high"]],
"2026-09-14":[["Revisar proyecto","09:00","high"],["Entregar documentación","14:30","medium"]],
"2026-09-16":[["Reunión de equipo","18:00","low"]]
};
let viewDate=new Date(2026,8,14);
let selectedDate=new Date(2026,8,14);

function dateKey(d){return d.getFullYear()+"-"+String(d.getMonth()+1).padStart(2,"0")+"-"+String(d.getDate()).padStart(2,"0");}

function renderDay(){
  const title=document.getElementById("selectedDateTitle");
  const list=document.getElementById("dayList");
  title.textContent=selectedDate.getDate()+" de "+months[selectedDate.getMonth()].toLowerCase();
  list.innerHTML="";
  const events=demoEvents[dateKey(selectedDate)]||[];
  if(!events.length){
    list.innerHTML='<div class="row"><div><b>Sin actividades de ejemplo</b><small>No hay recordatorios registrados para esta fecha.</small></div></div>';
    return;
  }
  events.forEach(([name,time,level])=>{
    const row=document.createElement("div");
    row.className="row";
    row.innerHTML='<i class="'+level+'"></i><div><b>'+name+'</b><small>'+time+'</small></div>';
    list.appendChild(row);
  });
}

function renderCalendar(){
  const calendar=document.getElementById("calendar");
  const title=document.getElementById("calendarTitle");
  calendar.innerHTML="";
  weekdays.forEach(w=>{const s=document.createElement("span");s.textContent=w;calendar.appendChild(s);});
  const y=viewDate.getFullYear(),m=viewDate.getMonth(),first=new Date(y,m,1);
  const start=(first.getDay()+6)%7;
  const days=new Date(y,m+1,0).getDate();
  const total=Math.ceil((start+days)/7)*7;
  title.textContent=months[m]+" "+y;
  for(let i=0;i<total;i++){
    const n=i-start+1;
    const d=new Date(y,m,n);
    const btn=document.createElement("button");
    btn.type="button"; btn.className="day";
    if(n<1||n>days){btn.classList.add("muted");btn.textContent=n<1?new Date(y,m,0).getDate()+n:n-days;}
    else{
      btn.textContent=n;
      if(d.getDay()===0||d.getDay()===6)btn.classList.add("weekend");
      if(dateKey(d)===dateKey(selectedDate))btn.classList.add("selected");
      if(demoEvents[dateKey(d)]){
        const dot=document.createElement("i");btn.appendChild(dot);
      }
      btn.addEventListener("click",()=>{selectedDate=new Date(y,m,n);renderCalendar();renderDay();});
    }
    calendar.appendChild(btn);
  }
}
document.getElementById("prevMonth").addEventListener("click",()=>{viewDate.setMonth(viewDate.getMonth()-1);renderCalendar();});
document.getElementById("nextMonth").addEventListener("click",()=>{viewDate.setMonth(viewDate.getMonth()+1);renderCalendar();});
renderCalendar();renderDay();

showView(location.hash.slice(1)||"inicio");
