function convertDate(data) {
	data = parseInt(data); 
	data2 = new Date(data * 1000);
	var month = data2.getMonth() + 1 ;
	if (month <= 9 ) {
		month = "0" + "" + month;
	}
	
	var dia = data2.getDate() ;
	if (dia <= 9 ) {
		dia = "0" + "" + dia;
	}
	
	var ano = data2.getFullYear();
	ano = " "+ano ;
	ano = ano.substr(3,6) ;
	
	var hora = data2.getHours() ;
	if (hora <= 9 ) {
		hora = "0" + "" + hora;
	}
	
	var minutos = data2.getMinutes() ;
	if (minutos <= 9 ) {
		minutos = "0" + "" + minutos;
	}
	
	
	var segundos = data2.getSeconds() ;
	if (segundos <= 9 ) {
		segundos = "0" + "" + segundos;
	}
	
	
	dataStr = dia +"/"+ month +"/"+ano+" as "+ hora +":"+ minutos +":"+ segundos ;
    dataStr = dia +" de "+ mes(month); 
	dataStr = mesUs(month) +" "+dia+" at "+ hora +":"+ minutos +"hr"
    //dataStr = month +"/"+ dia +"/"+ano+" at "+ hora +":"+ minutos +":"+ segundos ;
    //dataStr = month +"/"+ dia +" at "+ hora +":"+ minutos ;
    if(dataStr.indexOf("NaN") >= 1) {
        return "";
    } else
        return dataStr ;
}


function mesUs(m) {
    if(m == 1)  {
        return "Jan" ;
    } else
    if(m == 2)  {
        return "Feb";
    } else
    if(m == 3)  {
        return "Mar" ;
    } else
    if(m == 4)  {
        return "Apr"
    } else
    if(m == 5)  {
        return "May" ;
    } else
    if(m == 6)  {
        return "Jun" ;
    } else
    if(m == 7)  {
        return "Jul" ;
    } else
    if(m == 8)  {
        return "Aug" ;
    } else
    if(m == 9)  {
        return "Sep" ;
    } else
    if(m == 10)  {
        return "Oct" ;
    } else
    if(m == 11)  {
        return "Nov" ;
    } else
    if(m == 12)  {
        return "Dec" ;
    } 
}

function mes(m) {
    if(m == 1)  {
        return "Janeiro" ;
    } else
    if(m == 2)  {
        return "Fevereiro" ;
    } else
    if(m == 3)  {
        return "Mar&ccedil;o" ;
    } else
    if(m == 4)  {
        return "Abril"
    } else
    if(m == 5)  {
        return "Maio" ;
    } else
    if(m == 6)  {
        return "Junho" ;
    } else
    if(m == 7)  {
        return "Julho" ;
    } else
    if(m == 8)  {
        return "Agosto" ;
    } else
    if(m == 9)  {
        return "Setembro" ;
    } else
    if(m == 10)  {
        return "Outubro" ;
    } else
    if(m == 11)  {
        return "Novembro" ;
    } else
    if(m == 12)  {
        return "Dezembro" ;
    } 
}
